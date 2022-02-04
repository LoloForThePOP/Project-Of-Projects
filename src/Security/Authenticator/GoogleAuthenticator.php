<?php

namespace App\Security\Authenticator;

use App\Entity\Persorg;
use App\Entity\User; // your user entity
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class GoogleAuthenticator extends OAuth2Authenticator
{
    private $clientRegistry;
    private $entityManager;
    private $router;

    protected $slugger;

    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $entityManager, RouterInterface $router, SluggerInterface $slugger)
    {
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->router = $router;

        $this->slugger = $slugger;
    }

    public function supports(Request $request): ?bool
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'connect_google_check';
    }

    public function authenticate(Request $request): PassportInterface
    {
        $client = $this->clientRegistry->getClient('google_main');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function() use ($accessToken, $client) {
                
                $googleUser = $client->fetchUserFromToken($accessToken); //array containing data provided by google (for the property list : dd($client->fetchUserFromToken($accessToken)))

                $email = $googleUser->getEmail();

                /* // 1) have they logged in with Google before? (it might be more appropriate to check if a user Google Id has been saved in db like below, because user Google email might change as time goes by). I don't do it now because of MVP.
                $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['googleId' => $googleUser->["sub"]]); */

                $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
                
                if (!$user) { //creating a new user

                    $user = new User();

                    $userName = $googleUser->getName();

                    $user
                        ->setUserName($userName)
                        ->setUserNameSlug(strtolower($this->slugger->slug($userName)))
                        ->setEmail($email)
                        ->setPassword(substr(md5(rand()), 0, 7)) //creating a mock password hash
                        ->setParameter('isVerified', true);

                    $userPersorg = new Persorg(); // creating a user profile
                    $userPersorg->setName($user->getUserName());
                    $user->setPersorg($userPersorg);

                    $this->entityManager->persist($userPersorg);
                    $this->entityManager->persist($user);
                    $this->entityManager->flush();
                }

                return $user;

            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // change "app_homepage" to some route in your app
        $targetUrl = $this->router->generate('homepage');

        return new RedirectResponse($targetUrl);
    
        // or, on success, let the request continue to be handled by the controller
        //return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }
    

}
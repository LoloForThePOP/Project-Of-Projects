<?php

namespace App\Security\Authenticator;

use App\Entity\Persorg;
use App\Service\UserService;
use App\Entity\User; // your user entity
use App\Service\SessionVariablesService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class FacebookAuthenticator extends OAuth2Authenticator
{
    private $clientRegistry;
    private $entityManager;
    private $router;

    protected $slugger;
    protected $session;

    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $entityManager, RouterInterface $router, SluggerInterface $slugger, SessionInterface $session)
    {
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->router = $router;

        $this->slugger = $slugger;
        $this->session = $session;
    }

    public function supports(Request $request): ?bool
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'connect_facebook_check';
    }

    public function authenticate(Request $request): PassportInterface
    {
        $client = $this->clientRegistry->getClient('facebook_main');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function() use ($accessToken, $client) {
                
                $facebookUser = $client->fetchUserFromToken($accessToken); //array containing data provided by facebook (for the property list : dd($client->fetchUserFromToken($accessToken)))

                //dd($facebookUser);
                $email = $facebookUser->getEmail();

                /* // 1) have they logged in with Facebook before? (it might be more appropriate to check if a user Facebook Id has been saved in db like below, because user Facebook email might change as time goes by). I don't do it now because of MVP.
                $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['facebookId' => $facebookUser->["sub"]]); */

                $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
                
                if (!$user) { //creating a new user

                    $user = new User();
                    
                    $user
                        ->setUserName($facebookUser->getName())
                        ->setEmail($email)
                        ->setParameter("isVerified", true);

                    $userService = new UserService($this->entityManager, $this->slugger, $this->session, $user);
                    $userService->saveSolidUser(true);

                }
                
                return $user;

            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {

        $session = $request->getSession();

        if ($session->has("guest-user-id")) {
            //plutôt conserver dans la session le guest-user id, aller chercher ce guest user id dans le repo si je peux (normalement oui au pire si dessus fonction authenticate), cette présentation que l'on transfère à l'utilisateur actuel, et on supprime le guest-presenter. 
            //rechercher la présentation avec le guest-user token
            //dd($token);
        }
        
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
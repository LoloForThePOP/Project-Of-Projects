<?php

namespace App\Security\Authenticator;


use App\Service\CreateUserService;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
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

    protected $createUserService;

    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $entityManager, RouterInterface $router, CreateUserService $createUserService)
    {
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->router = $router;

        $this->createUserService = $createUserService; // to save the authenticating user in db if not already done (Google shares user email, name)

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

                $email = $googleUser->getEmail(); //User email as provided by Google

                // Have user logged in with Google before?

                $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]); //it might be more appropriate to check if a user Google Id has been saved in db like below, because user Google email might change as time goes by. I don't do it now because of MVP. $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['googleId' => $googleUser->["sub"]]);
                
                if (!$user) { //creating a new user

                    $user = new User();

                    $userName = $googleUser->getName(); //User name as provided by Google

                    $user //hydrating the user object
                        ->setUserName($userName)
                        ->setEmail($email)
                        ->setParameter('isVerified', true);

                    $this->createUserService->saveSolidUser($user, false, null, false); // to save user object in db, without creating a username (we take the one provided by Google), creating a dumb password (user connects with the Google one), without sending a confirmation email (Facebook already verified user).

                }

                return $user;

            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // redirecting to the route whereby we manage post auth
        return new RedirectResponse($this->router->generate('auth_redirections'));
        
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }
    

}
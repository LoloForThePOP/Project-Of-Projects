<?php

namespace App\Security\Authenticator;

use App\Service\CreateUserService;
use App\Entity\User; // your user entity
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


/**
 * Allows user to authenticate to the app with a Facebook account
 */
class FacebookAuthenticator extends OAuth2Authenticator
{
    private $clientRegistry;
    private $entityManager;
    private $router;
    private $createUserService;

    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $entityManager, RouterInterface $router, CreateUserService $createUserService)
    {
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->router = $router;

        $this->createUserService = $createUserService; // to save the authenticating user in db if not already done (facebook shares user email, name)
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

                $email = $facebookUser->getEmail(); //User email as provided by Facebook

                /* Have user logged in with Facebook before? */

                $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]); // it might be more appropriate to check if a user Facebook Id has been saved in db like below, because user Facebook email might change as time goes by). I don't do it now because it is an mvp.                 $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['facebookId' => $facebookUser->["sub"]]);
                
                if (!$user) { //if user email has not been find in db we consider it is a new user

                    $user = new User();
                    
                    $user //hydrating the user object
                        ->setUserName($facebookUser->getName()) //User name as provided by Facebook
                        ->setEmail($email)
                        ->setParameter("isVerified", true); //Facebook already did it

                    $this->createUserService->saveAuthenticatedUser($user, false, null, false); // to save user object in db, without creating a username (we take the one provided by facebook), creating a dumb password (user connects with the facebook one), without sending a confirmation email (Facebook already verified user).

                }
                
                return $user;

            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {

        // redirecting user to the route whereby we manage post auth
        return new RedirectResponse($this->router->generate('auth_redirections'));
        
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }
    

}
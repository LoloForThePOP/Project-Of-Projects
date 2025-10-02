<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\CreateUserService;
use App\Form\RegistrationFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RegistrationController extends AbstractController
{
    /**
     * 
     * Allows to register to the app via an email address + password
     * 
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, CreateUserService $createUserService): Response
    {

        //registration form with antispam time protection
        $form = $this->createForm(
            RegistrationFormType::class,
            array(

                'antispam_time'     => true,
                'antispam_time_min' => 5,
                'antispam_time_max' => 1200
            )
        );

        $form->handleRequest($request);

        //If a valid form is submitted
        if ($form->isSubmitted() && $form->isValid()) {

            $plainPassword = $form->get("plainPassword")->getData();

            $user = new User();
            $user->setEmail($form->get("email")->getData())
                 ->setPassword($plainPassword);

            $createUserService->saveAuthenticatedUser($user, true, $plainPassword, true); //saving user, creating a random username, without creating a password, with email verification.

            return $this->render('registration/please_confirm_email.html.twig', [//redirecting user to a page whereby we ask him to check emails
                'userEmailToConfirm' => $user->getEmail(),// we pass user provided email address so that he can check if he has provided her good one without typo
                'websiteSupportEmail' => $this->getParameter('app.email.contact'),// in case user can't confirm his / her email address we provide email address from app support.
            ]);
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * User email confirmation route after registering to the app (see create user service for details about this email).
     * 
     * URL params: $token: the token we assigned to the newly registered user (token stored in DB, see User entity for details), this token is also assigned to an email we sent to user so that if user can confirm their email address.
     * 
     * @Route("/verify/email_confirmation/{token}", name="email_confirmation")
     */
    public function EmailConfirmation(string $token): Response
    {

        //Searching user with provided in url token

        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(

            ['emailValidationToken' => $token]

        );

        //If user does not exist in DB, redirecting user to app login route and displaying an error
        if ($user === null) {
        
            $this->addFlash('danger', 'Une erreur est survenue : Token Inconnu');
            return $this->redirectToRoute('app_login');
        }

        //Else setting that user email is verified

        $user->setParameter('isVerified', true);//$parameter is an User entity attribute array containing info about user 
        $user->setEmailValidationToken(null);//no need to keep the email validation token filled

        $entityManager = $this->getDoctrine()->getManager()->flush();

        //Redirecting user to login route with a successfull feedback flash message 
        $this->addFlash('success', 'Votre adresse a été vérifiée ! Vous pouvez maintenant vous connecter et utiliser le site.');

        return $this->redirectToRoute('app_login');
    }

}

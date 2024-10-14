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
        $user = new User();

        //setting a temporary user name (this is a required field, a random one is generated in the CreateUserService used bellow, user can change it later)
        $user->setUserName("temp");

        //registration form with antispam time protection
        $form = $this->createForm(
            RegistrationFormType::class,
            $user,
            array(

                'antispam_time'     => true,
                'antispam_time_min' => 5,
                'antispam_time_max' => 1200
            )
        );

        $form->handleRequest($request);

        //If a valid form is submitted
        if ($form->isSubmitted() && $form->isValid()) {

            $plainPassword = $form->getData("password");

            $createUserService->saveSolidUser($user, true, $plainPassword, true); //saving user, creating a random username, without creating a password, with email verification.

            return $this->render('registration/please_confirm_email.html.twig', [//redirecting user to a page whereby we ask him to check emails
                'userEmailToConfirm' => $user->getEmail(),// we pass user provided email address so that he can check if he has provided a good one ( without typo)
                'websiteSupportEmail' => $this->getParameter('app.general_contact_email'),// in case user can't confirm his / her email address we provide email address from app support.
            ]);
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * User Email Confirmation Action
     * 
     * @Route("/verify/email_confirmation/{token}", name="email_confirmation")
     */
    public function EmailConfirmation(Request $request, string $token): Response
    {

        // searching user with given token

        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(

            ['emailValidationToken' => $token]

        );

        // if user does not exist
        if ($user === null) {
            // displaying an error
            $this->addFlash('danger', 'Une erreur est survenue : Token Inconnu');
            return $this->redirectToRoute('app_login');
        }

        $user->setParameter('isVerified', true);
        $user->setEmailValidationToken(null);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        // flash message & redirect to login route
        $this->addFlash('success', 'Votre adresse a été vérifiée ! Vous pouvez maintenant vous connecter et utiliser le site.');

        return $this->redirectToRoute('app_login');
    }
}

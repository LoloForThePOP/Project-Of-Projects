<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EmailFormType;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use App\Form\ForgottenPasswordCreationType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class ResetUserPasswordController extends AbstractController
{

    /**
     * 
     * forgotten password : user provides her email and receive an email with reset password token. 
     * 
     * @Route("/forgotten-password-request", name="forgotten_password_request")
     * 
     */
    public function forgottenPassword(
        Request $request,
        UserRepository $userRepo,
        TokenGeneratorInterface $tokenGenerator,
        MailerInterface $mailer
    ): Response {

        $form = $this->createForm(EmailFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $userRepo->findOneByEmail($form->getData()['email']);

            if ($user === null) {

                $this->addFlash('danger', 'Cette adresse e-mail est inconnue');
                return $this->redirectToRoute('app_login');
            }

            // generating a token
            $token = $tokenGenerator->generateToken();

            try {
                $user->setResetPasswordToken($token);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
            } catch (\Exception $e) {
                $this->addFlash('warning', $e->getMessage());
                return $this->redirectToRoute('app_login');
            }

            // Password re-initialisation link
            $url = $this->generateUrl('forgotten_password_create', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);

            // send an email to verify user adress

            $email = (new TemplatedEmail())
                ->from($this->getParameter('app.mailer_email'))
                ->to(new Address($user->getEmail()))
                ->subject('Réinitialisation Mot de Passe - Projet des Projets')

                // path of the Twig template to render
                ->htmlTemplate('reset_user_password/email_confirm_token.html.twig')

                // pass variables (name => value) to the template
                ->context([
                    'confirmationURL' => $url,
                ]);

            $mailer->send($email);

            $this->addFlash('success', 'Un e-mail de réinitialisation de votre mot de passe vous a été envoyé. Ouvrez le. À bientôt.');

            return $this->redirectToRoute('homepage');
        }

        return $this->render(
            'reset_user_password/forgotten_password_request.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * forgotten password : user create a new password
     * 
     * @Route("/forgotten-password-create-new/{token}", name="forgotten_password_create")
     * 
     */
    public function resetPassword(Request $request, string $token, UserPasswordHasherInterface  $hasher)
    {

        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['resetPasswordToken' => $token]);

        if ($user === null) {
            $this->addFlash('danger', 'Une erreur est survenue : Token Inconnu');
            return $this->redirectToRoute('app_login');
        }

        // new password form

        $form = $this->createForm(ForgottenPasswordCreationType::class);

        $form->get('token')->setData($token);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setResetPasswordToken(null);

            // encoding new user password

            $user->setPassword(
                $hasher->hashPassword(
                    $user,
                    $form->get('newPassword')->getData()
                )
            );

            // saving changes

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre nouveau mot de passe est enregistré. Vous pouvez désormais l\'utiliser.');

            return $this->redirectToRoute('app_login');
        }

        // displaying new user password creation form

        return $this->render(
            'reset_user_password/forgotten_password_creation.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}

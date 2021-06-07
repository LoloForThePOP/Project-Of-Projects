<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // email validation
            // generate a token
            $token = $tokenGenerator->generateToken();

            // save new user in database
            try {

                $user->setParameters(['emailValidationToken' => $token]);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
            } catch (\Exception $e) {
                $this->addFlash('warning', $e->getMessage());
                return $this->redirectToRoute('account_login');
            }

            // generate url for user email confirmation
            $confirmationURL = $this->generateUrl('email_confirmation', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);

            // send an email to verify user adress

            $email = (new TemplatedEmail())
                ->from($this->getParameter('app.mailer_email'))
                ->to(new Address($user->getEmail()))
                ->subject('Merci de confirmer votre email')

                // path of the Twig template to render
                ->htmlTemplate('registration/email_confirm_email.html.twig')

                // pass variables (name => value) to the template
                ->context([
                    'confirmationURL' => $confirmationURL,
                ]);

            $mailer->send($email);

            // asking user to validate his email adress

            return $this->render('registration/please_confirm_email.html.twig', [
                'userEmailToConfirm' => $user->getEmail(),
                'websiteSupportEmail' => $this->getParameter('app.support_email'),
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

            ['parameters' => ['emailValidationToken' => $token]]

        );

        // if user does not exist
        if ($user === null) {
            // On affiche une erreur
            $this->addFlash('danger', 'Une erreur est survenue : Token Inconnu');
            return $this->redirectToRoute('account_login');
        }

        // else if post method
        if ($request->isMethod('POST')) {

            // token deletion
            $user->setParameters(['emailValidationToken' => null]);

            // On chiffre le mot de passe
            $user->setHash($passwordEncoder->encodePassword($user, $request->request->get('password')));

            // On stocke
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // On crée le message flash
            $this->addFlash('success', 'Votre nouveau mot de passe est enregistré. Vous pouvez désormais l\'utiliser.');

            // On redirige vers la page de connexion
            return $this->redirectToRoute('account_login');
        } else {
            // Si on n'a pas reçu les données, on affiche le formulaire
            return $this->render('account/forgotten_password_create.html.twig', ['token' => $token]);
        }
        return $this->redirectToRoute('registration/please_confirm_email.html.twig', [
            'userEmailToConfirm' => $request->query->get('userEmail'),
            'websiteSupportEmail' => $this->getParameter('app.support_email'),
        ]);
    }
}

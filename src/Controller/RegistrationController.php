<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Persorg;
use App\Form\RegistrationFormType;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator, SluggerInterface $slugger): Response
    {
        $user = new User();
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

        if ($form->isSubmitted() && $form->isValid()) {

            // encode the plain password
            $user->setPassword(
                $passwordHasher->hashPassword($user, $form->get('plainPassword')->getData()));

            // email validation
            // generate a token
            $token = $tokenGenerator->generateToken();

                
            // creating a unique username slug
            $slug = strtolower($slugger->slug($user->getUserName()));

            $slugs = $this->getDoctrine()->getRepository(User::class)->createQueryBuilder('u')->where('u.userNameSlug LIKE :slug')->setParameter('slug', $slug.'%')->getQuery()->getResult();

            if (! empty($slugs)) {

                $slug .= '-' . count($slugs); //this method does not work if user rows can be deleted + it does not provide reliable increment (ex : 1) My post -> my-post 2) My -> my-1 (instead of my))

            }    

            $user->setUserNameSlug($slug);  

            // creating an user's public profile

            $persorg = new Persorg();
            $persorg->setName($user->getUserName());
            $user->setPersorg($persorg);

            // save new user in database
            try {

                $user->setEmailValidationToken($token);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

            } catch (\Exception $e) {
                $this->addFlash('warning', $e->getMessage());
                return $this->redirectToRoute('app_login');
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

        // flash message & redirect to toute login
        $this->addFlash('success', 'Votre addresse a été vérifiée ! Vous pouvez maintenant vous connecter et utiliser le site.');

        return $this->redirectToRoute('app_login');
    }
}

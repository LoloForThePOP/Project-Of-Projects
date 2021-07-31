<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\PersorgType;
use App\Form\UpdateEmailType;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisteredUserController extends AbstractController
{
    /**
     * @Route("/user/profile/{userNameSlug}", name="show_user_profile")
     */
    public function showProfile(User $user): Response
    {
        return $this->render('user/show_profile.html.twig', [

            'user' => $user,

        ]);
    }

    /**
     * Allow user to manage its public profile
     * 
     * @Route("account/public-profile/", name="edit_public_profile")
     * 
     * @Security("is_granted('ROLE_USER')")
     * 
     * @return Response
     */
    public function publicProfile(Request $request, EntityManagerInterface $manager){

        $userPersorg = $this->getUser()->getPersorg();

        $persorgForm = $this->createForm(PersorgType::class, $userPersorg); 

        $persorgForm->handleRequest($request);

        if($persorgForm->isSubmitted() && $persorgForm->isValid()){

            $this->getUser()->setUserName($persorgForm->get('name')->getData());

            $manager->flush();

            $this->addFlash(
                'success',
                'Les Modifications ont étées enregistrées.'
            );

            return $this->redirectToRoute('show_user_profile',[
                'userNameSlug' => $this->getUser()->getUserNameSlug(),
            ]);
        }

        return $this->render('/user/edit_persorg.html.twig',[
     
            'persorgForm' => $persorgForm->createView(),
            'userPersorg' => $userPersorg,
        ]);

    }


      /**
     * Allow user to access update account possibilities menu
     * 
     * @Route("account/update-menu",name="update_account_menu")
     * 
     * @Security("is_granted('ROLE_USER')")
     * 
     * @return Response
     */
    public function accessUpdateAccountMenu(Request $request, EntityManagerInterface $manager){

        return $this->render('user/update_account_menu.html.twig',[
        ]);
    }


    
    /**
     * Allow user to modify his account email
     * 
     * @Route("account/update-email", name="update_account_email")
     * 
     * @return Response
     */
    public function updateEmail(Request $request, EntityManagerInterface $manager){

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();

        $form = $this->createForm(UpdateEmailType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $manager->flush();

            $this->addFlash(
                'success',
                'Adresse e-mail modifiée.'
            );

            return $this->redirectToRoute('show_user_profile',[
                'userNameSlug' => $this->getUser()->getUserNameSlug(),
            ]);
        }

        return $this->render('user/update_account_email.html.twig',[
            'form' => $form->createView(),
        ]);
    }


    
    
    /**
     * Allow user to update his password
     * 
     * @Route("/account/update-password",name="update_account_password")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function updatePassword(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $manager){

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $user = $this->getUser();
        
        $form=$this->createForm(UpdatePasswordType::class);

        $form->handleRequest($request);

        return $this->render('/user/update_password.html.twig',[
            'form' => $form->createView(),
            
        ]);

    }






    
}

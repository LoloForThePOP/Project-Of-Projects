<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RegisteredUserController extends AbstractController
{
    /**
     * @Route("/user/profile/{username}", name="show_user_profile")
     */
    public function showProfile(User $user): Response
    {
        return $this->render('user/show_profile.html.twig', [

            'user' => $user,

        ]);
    }
}

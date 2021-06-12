<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegisteredUserController extends AbstractController
{
    /**
     * @Route("/user/profile", name="show_user_profile")
     */
    public function showProfile(): Response
    {
        return $this->render('user/show_profile.html.twig', []);
    }
}

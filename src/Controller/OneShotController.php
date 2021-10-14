<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * This controller allows to perform an action that will be executed only once. Exemple : change something in database for all users.
 * 
 * It is only accessed for admins thanks to  /admin/ route
 * 
 * It is safe to delete the method content once it's been applied
 * 
 */
class OneShotController extends AbstractController
{
    /**
     * @Route("/admin/one-shot", name="one_shot")
     * 
     */
    public function index(): Response
    {
        return $this->render('one_shot/index.html.twig', [
            'controller_name' => 'OneShotController',
        ]);
    }
}

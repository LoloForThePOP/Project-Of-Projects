<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StaticController extends AbstractController
{
    
    /**
     * @Route("/static/{page_name}", name="static")
     */
    public function route($page_name): Response
    {

        return $this->render('static/'.$page_name.'.html.twig', [
            'title' => $page_name,
        ]);

    }
   

}

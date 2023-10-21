<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AIPresentationHelperController extends AbstractController
{
    #[Route('/ia-assistant-gratuit-de-presentation-de-projet', name: 'ai_presentation_helper')]
    public function index(): Response
    {
        return $this->render('ai_presentation_helper/origin.html.twig', [
            'controller_name' => 'AIPresentationHelperController',
        ]);
    }
}

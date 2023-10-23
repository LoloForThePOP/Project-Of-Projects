<?php

namespace App\Controller;

use App\Form\AIPPAdviceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AIPresentationHelperController extends AbstractController
{
    #[Route('/ia-assistant-gratuit-de-presentation-de-projet', name: 'ai_presentation_helper')]
    public function index(Request $request): Response
    {

        $form = $this->createForm(AIPPAdviceType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

           //dd("form submitted and valid");

        }

        return $this->render('ai_presentation_helper/origin.html.twig', [
            'form' => $form->createView(),
        ]);




    }
}

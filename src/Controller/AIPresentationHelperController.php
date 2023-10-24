<?php

namespace App\Controller;

use OpenAI;
use App\Form\AIPPAdviceType;
use App\Service\OpenAIService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AIPresentationHelperController extends AbstractController
{
    
    /**
     * @Route("/ia-assistant-gratuit-de-presentation-de-projet", name="ai_presentation_helper_origin")
     */
    public function origin(Request $request): Response
    {


        // Dans le deuxième contrôleur
        $data = $this->get('session')->get('key');

                $form = $this->createForm(AIPPAdviceType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $target = $form->get('ppTarget')->getData();
            $format = $form->get('ppFormat')->getData();

            $ia = OpenAI::client($_ENV['OPEN_AI_KEY']);

            $messages =  [
                ['role' => 'system', 'content' => "Tu es un coach en présentation de projet. Tu vouvoies la personne. Tu lui donnes une liste de 10 conseils sous la forme d'une liste html, chaque item de la liste avec une îcone font-awesome appropriée. Le résultat doit être responsive."],
                ['role' => 'user', 'content' => 'Peux-tu donner des conseils je présente mon projet à '.$target.', le format de la présentation est'.$format.'.'],
            ];
    
            $response = $ia->chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => $messages,
            ]);
            
            
            $response->toArray(); // ['id' => 'chatcmpl-6pMyfj1HF4QXnfvjtfzvufZSQq6Eq', ...]
    
            $this->get('session')->set('generalAdvice', $response['choices'][0]['message']['content']);

            return $this->redirectToRoute('ai_presentation_helper_assistant', [
                
            ]);

        }

        return $this->render('ai_presentation_helper/origin.html.twig', [
            'form' => $form->createView(),
        ]);

    }
    
    
    /**
     * @Route("//ia-assistant-gratuit-de-presentation-de-projet-contenu/", name="ai_presentation_helper_assistant")
     */
    public function content(): Response
    {

        dd($$this->get('session')->get('generalAdvice'));
        return $this->render('ai_presentation_helper/assistant.html.twig', [
        ]);

    }

    /**
     * @Route("/ajax-ia-assistant-gratuit-de-presentation-de-projet", name="ajax_ai_presentation_helper_assistant")
     */
    public function ajaxOriigin(Request $request) {
        
        if ($request->isXmlHttpRequest()) {

            $userMessage = $request->request->get('message');

            // Vous pouvez traiter le message ici (par exemple, en utilisant une IA, une base de données, etc.)
            // Pour cet exemple, nous allons simplement renvoyer une réponse statique.
    
            $response = "Ceci est la réponse du coach à votre message : " . $userMessage;
    
            // Vous pouvez renvoyer la réponse sous forme de JSON
            return new JsonResponse(['data' => $response]);
          
        }

    }







}

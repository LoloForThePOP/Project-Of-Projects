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
        
/*         $client = OpenAI::client($_ENV['OPEN_AI_KEY']);
        
        $result = $client->completions()->create([
            'model' => 'gpt-3.5-turbo-instruct',
            'prompt' => 'PHP is',
        ]);
        
        dd($result['choices'][0]['text']);  */

        $form = $this->createForm(AIPPAdviceType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $target = $form->get('ppTarget')->getData();
            $format = $form->get('ppFormat')->getData();

            $ia = OpenAI::client($_ENV['OPEN_AI_KEY']);

            $messages =  [

                ['role' => 'system', 'content' => "Tu es un coach en présentation de projet. Pour parler tu utilises le vouvoiement en français et pas le tutoiement. Tu donnes à ton élève une liste de 10 conseils sous la forme d'une liste html, chaque item de la liste est illustré à gauche avec une îcone font-awesome appropriée (inclure la librairie font-awesome). Utilise un titre avant la liste de conseils (ce titre rappelle d'une part a qui l'utilisateur présente son projet et ce titre rappelle d'autre part quel est le format de sa présentation, par exemple télévision, powerpoint, en fonction de ce que dit l'utilisateur). Tu utilises une conclusion après la liste de conseils, cette conclusion est une seule phrase qui apporte des conseils généraux, elle est encourageante. Tu n'utilises pas de phrase d'introduction dans ta réponse. L'ensemble de ta réponse est inclus dans une balise div avec la classe generalAdvice."],

                ['role' => 'user', 'content' => "Peux-tu me donner des conseils, je présente mon projet à ".$target.", le format de la présentation c'est ".$format."."],

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

        $generalAdvice = $this->get('session')->get('generalAdvice');

        return $this->render('ai_presentation_helper/assistant.html.twig', [
            'generalAdvice' => $generalAdvice,
        ]);

    }

    /**
     * @Route("/ajax-ia-assistant-gratuit-de-presentation-de-projet", name="ajax_ai_presentation_helper_assistant")
     */
    public function ajaxOrigin(Request $request) {
        
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

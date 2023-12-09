<?php

namespace App\Controller;

use OpenAI;
use App\Form\AIPPAdviceType;
use App\Entity\CollectedData;
use App\Service\OpenAIService;
use Symfony\Component\Mime\Email;
use App\Service\DataCollectService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AIPresentationHelperController extends AbstractController
{
    
    /**
     * @Route("/ia-assistant-gratuit-de-presentation-de-projet", name="ai_presentation_helper_origin")
     */
    public function origin(DataCollectService $dataCollect, MailerInterface $mailer, Request $request): Response
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


            //save data to db
            $dataCollectArray = [
                "presentationTarget" => $target,
                "presentationFormat" => $format,
            ];

            $dataCollect->save("ai_presentation_helper", [$dataCollectArray]);

            //email admin
            $sender = $this->getParameter('app.general_contact_email');
            $receiver = $this->getParameter('app.general_contact_email');

            $email = (new Email())
                ->from($sender)
                ->to($receiver)
                ->subject('New AI Coach Usage')
                ->html('<pre>'.json_encode($dataCollectArray, JSON_PRETTY_PRINT).'</pre>');

            $mailer->send($email);

            $ia = OpenAI::client($_ENV['OPEN_AI_KEY']);

            $messages =  [

                ['role' => 'system', 'content' => "Tu es un coach en présentation de projet. Tu donnes à ton élève une liste de 10 conseils, tu réponds sous forme d'une liste html, chaque item de la liste est illustré à gauche avec une icône font-awesome distincte et si possible appropriée. Tu ajoutes une couleur différente à chaque icône (sauf le jaune). Tu utilises une conclusion en une seule phrase après la liste de conseils, cette conclusion apporte des conseils généraux, elle est encourageante. Tu n'utilises pas de phrase d'introduction dans ta réponse. L'ensemble de ta réponse est inclus dans une balise div avec la classe generalAdvice. Utilise un titre avant la liste de conseils. Ce titre rappelle d'une part à quel type de personne l'utilisateur présente son projet, par exemple des cuisiniers, un journaliste, en fonction de ce que te dira l'utilisateur. Ce titre rappelle d'autre part quel est le support de sa présentation, par exemple télévision, powerpoint, en fonction de ce que te dira l'utilisateur. Ne pas utiliser de markdown."],

                ['role' => 'user', 'content' => "Peux-tu me donner des conseils, je présente mon projet au type de personne suivant : ".$target.", le support utilisé pour ma présentation (et non pas mon projet) c'est : ".$format."."],

            ];
    
            $response = $ia->chat()->create([
                'model' => 'gpt-3.5-turbo-1106',
                'messages' => $messages,
            ]);
            
            
            $response->toArray();

            $responseContent = $response['choices'][0]['message']['content'];


            //save data
            $dataCollectArray["ai_answer"] = $responseContent;
            $dataCollect->save("ai_presentation_helper", [$dataCollectArray]);



    
            $this->get('session')->set('generalAdvice', $responseContent);

            return $this->redirectToRoute('ai_presentation_helper_assistant', [
                
            ]);

        }

        return $this->render('ai_presentation_helper/origin.html.twig', [
            'form' => $form->createView(),
        ]);

    }
    
    
    /**
     * @Route("/ia-assistant-gratuit-de-presentation-de-projet-reponse/", name="ai_presentation_helper_assistant")
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

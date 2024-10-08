<?php

namespace App\Controller;

use OpenAI;

use App\Form\AIPPAdviceType;

use App\Service\ImageService;
use App\Service\AILogoService;
use App\Service\MailerService;
use App\Service\CreatePPService;
use App\Service\DataCollectService;
use App\Service\AI\AICreatePPService;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * Ai is used to help user present its project
 */
class AIPresentationHelperController extends AbstractController
{

    /**
     * 
     * Allow to give user a list of 10 project presentation advices (user connected or not).
     * Using two controller methods:
     *  - Here (ai_presentation_helper_origin) we provide user a form and ai treat submitted data.
     *  - AI response display is done using another route bellow (ai_presentation_helper_assistant)
     * 
     * @Route("/ia-assistant-gratuit-de-presentation-de-projet", name="ai_presentation_helper_origin")
     */
    public function origin(DataCollectService $dataCollect, MailerInterface $mailer, Request $request): Response
    {
        //This form asks user some basic questions (see it)
        $form = $this->createForm(AIPPAdviceType::class);

        $form->handleRequest($request);

        //If form is submitted & valid

        if ($form->isSubmitted() && $form->isValid()) {

            $target = $form->get('ppTarget')->getData(); //getting project presentation targeted audience
            $format = $form->get('ppFormat')->getData(); //getting project presentation format

            /* Saving user's provided data to DB (see DataCollectService) */

            //Data collection
            $dataCollectArray = [
                "presentationTarget" => $target,
                "presentationFormat" => $format,
            ];

            //Data storage
            $dataCollect->save("ai_presentation_helper", [$dataCollectArray]);

            /* Email an admin that someone used the product (october 2024 it's still so rare!) */
            $sender = $this->getParameter('app.general_contact_email');
            $receiver = $this->getParameter('app.general_contact_email');

            $email = (new Email())
                ->from($sender)
                ->to($receiver)
                ->subject('New AI Coach Usage')
                ->html('<pre>'.json_encode($dataCollectArray, JSON_PRETTY_PRINT).'</pre>');

            $mailer->send($email);

            /* AI usage */

            //Open AI client
            $ia = OpenAI::client($_ENV['OPEN_AI_KEY']);

            //Prompt "create a list of ten advices with user provided data"

            $messages =  [

                ['role' => 'system', 'content' => "Tu es un coach en présentation de projet. Tu donnes à ton élève une liste de 10 conseils sous forme d'une liste html, chaque item de la liste est illustré à gauche avec une icône font-awesome distincte et si possible appropriée. Tu ajoutes une couleur différente à chaque icône (sauf le jaune et des couleurs non fluo, non criardes). Tu utilises une conclusion en une seule phrase après la liste de conseils, cette conclusion encouragente apporte des conseils généraux, elle est incluse dans une balise de paragraphe. Tu n'utilises pas de phrase d'introduction dans ta réponse. L'ensemble de ta réponse est inclus dans une balise div avec la classe generalAdvice. Utilise un titre avant la liste de conseils. Ce titre rappelle d'une part à quel type de personne l'utilisateur présente son projet, par exemple des cuisiniers, un journaliste, en fonction de ce que te dira l'utilisateur. Ce titre rappelle d'autre part quel est le support de sa présentation, par exemple télévision, powerpoint, en fonction de ce que te dira l'utilisateur. Ne pas utiliser de markdown."],

                ['role' => 'user', 'content' => "Peux-tu me donner des conseils, je présente mon projet au type de personne suivant : ".$target.", le support utilisé pour ma présentation (et non pas mon projet) c'est : ".$format."."],

            ];
            
            //Getting AI response and selecting usefull content
            $response = $ia->chat()->create([
                'model' => 'gpt-3.5-turbo-0125',
                'messages' => $messages,
            ]);
            
            $response->toArray();

            $responseContent = $response['choices'][0]['message']['content'];


            /* Saving AI provided data to DB for product improvement */
            $dataCollectArray["ai_answer"] = $responseContent;
            $dataCollect->save("ai_presentation_helper", [$dataCollectArray]);

            /* Saving AI response in a session because we display it with another route */
            $this->get('session')->set('generalAdvice', $responseContent);

            return $this->redirectToRoute('ai_presentation_helper_assistant', [//The route whereby we display AI response to user
                
            ]);

        }

        return $this->render('ai_presentation_helper/presentation_advice/origin.html.twig', [//the route whereby the initial form is displayed to user
            'form' => $form->createView(),
        ]);

    }
    
    
    /**
     * This method get the AI answer (advice to present a project) in session and display it in a twig template.
     * 
     * @Route("/ia-assistant-gratuit-de-presentation-de-projet-reponse/", name="ai_presentation_helper_assistant")
     */
    public function content(): Response
    {
        
        $generalAdvice = $this->get('session')->get('generalAdvice');

        return $this->render('ai_presentation_helper/presentation_advice/assistant.html.twig', [
            'generalAdvice' => $generalAdvice, //the AI answer
        ]);

    }
    
    
    /**
     * @Route("/ia-assistant-gratuit-de-presentation-de-projet-creation-logo/", name="ai_presentation_helper_assistant_logo")
     */
    public function logo(): Response
    {

        return $this->render('ai_presentation_helper/logo_creation/origin.html.twig', [
            
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

    /**
     * @Route("/ajax-ia-assistant-gratuit-de-creation-de-logo", name="ajax_ia_logo_creation_helper")
     */
    public function ajaxCreateLogo(Request $request, ImageService $imageService, AILogoService $aiLogoService) {
       
        if ($request->isXmlHttpRequest()) {

            session_write_close();

            $dataArray = $request->request->get('data');
            $prompt = $aiLogoService->createPrompt($dataArray);

            $ia = OpenAI::client($_ENV['OPEN_AI_KEY']);
        
            $response = $ia->images()->create([
                'model' => 'dall-e-3',
                'prompt' => $prompt,
                'size' => "1024x1024",
                'n' => 1,
                'response_format' => 'url',
            ]);
        
            $response->created; // 1589478378
            
            foreach ($response->data as $data) {
                $data->url;
                $data->b64_json; // null
            }
            
            $response->toArray(); 

            $imageUrl = $response["data"][0]["url"];


            $generatedImagePath = $imageService->saveImageFromUrlToPath($imageUrl, 'public/ia-generated-logos');
            $imagesPathsArray = $imageService->splitImage($generatedImagePath);
    
            // Vous pouvez renvoyer la réponse sous forme de JSON
            return new JsonResponse([
                'prompt' => $prompt,
                'imagesPathsArray' => $imagesPathsArray,
            ]);
          
        }

    }


     /**
     * @Route("/ia-assistant-gratuit-entretien-projet/", name="ai_interview_helper_origin")
     */
    public function interviewOrigin(Request $request): Response
    {

        $context = $request->query->get('context'); //interview origin display can vary depending on context (see twig template)

        $projectGoal = $this->get('session')->get('project_goal'); //maybe we already know project goal (ex: random user filled homepage form)       

        $this->get('session')->set('ai_interview_helper_conversation', null);
        $this->get('session')->set('ai_interview_helper_conversation_count_user_interactions', 0);

        return $this->render('ai_presentation_helper/interview/origin.html.twig', [
            'context' => $context,
            "projectGoal" => $projectGoal,
        ]);

    }


     /**
     * @Route("/ajax-ia-assistant-gratuit-entretien-projet", name="ajax_ai_interview_helper_origin")
     */
    public function ajaxInterviewOrigin(Request $request, DataCollectService $dataCollect, MailerService $mailerService) {
         
        if ($request->isXmlHttpRequest()) {

            $userMessage = $request->request->get('userMessage');

            $ia = OpenAI::client($_ENV['OPEN_AI_KEY']);

            //case new conversation, we initiate it
            if ($this->get('session')->get('ai_interview_helper_conversation') == null) {

                //dump("new session");
                
                $messages =  [

                    ['role' => 'system', 'content' => "Vous êtes un coach expert en présentation de projet. Vous ne donnez aucun conseil pour réaliser le projet, vous donnez seulement de l'aide pour PRÉSENTER le projet à une ou plusieurs personnes (exemple: un maire, un jury d'investisseurs...). Vous demandez à l'utilisateur de clarifier l'objectif de son projet si besoin. Si besoin vous demandez des précisions à l'utilisateur. Vous savez poser les bonnes questions et vous aidez l'utilisateur à répondre à ces questions. Vous posez une seule et seulement une seule question à la fois."],

                    ['role' => 'user', 'content' => "Voici l'objectif de mon projet : ".$userMessage."."],

                ];
                
            } else {

                //dump("prolongated conversation");

                $userAnswerAIRow = ['role' => 'user', 'content' => $userMessage];

                //Prolongating previous conversation messages

                $messages = $this->get('session')->get('ai_interview_helper_conversation');
                $messages[] = $userAnswerAIRow;

            }

            $response = $ia->chat()->create([
                'model' => 'gpt-3.5-turbo-0125',
                'messages' => $messages,
            ]);
            
            
            $response->toArray();
            $responseContent = $response['choices'][0]['message']['content'];


            //Storing ai answer
            $assistantAnswerAIRow = ['role' => 'assistant', 'content' => $responseContent];

            $messages[] = $assistantAnswerAIRow;

            //Storing conversation as it is now
            $this->get('session')->set('ai_interview_helper_conversation', $messages);

            
            // if user interactions are long enough : collecting conversation data and signaling that to webmaster
            $abr="ai_interview_helper_conversation_count_user_interactions";
            $gabr=$this->get('session')->get($abr);

            $this->get('session')->set($abr, $gabr + 1);

            if ($gabr == 3) {
                $dataCollectArray=[];
                $dataCollectArray["conversation"] = $messages;
                $dataCollect->save("ai_presentation_interview_helper", $dataCollectArray);

                //email admin

                $mailerService->mailAdmin("New AI Presentation Interview Coach Usage", '<pre>'.json_encode($dataCollectArray, JSON_PRETTY_PRINT).'</pre>');

            }

            $aiAnswer = $responseContent;
    
            return new JsonResponse(['aiAnswer' => $aiAnswer]);
          
        }
   
    }





    /**
    * @Route("/interview-ai-presentation-interview-helper/create-ppp", name="ai_create_ppp")
    */
    public function aiCreatePPP(AICreatePPService $createSummaryService, CreatePPService $createPPService, MailerService $mailerService) {

        $conversationRawData = $this->get('session')->get('ai_interview_helper_conversation');

        if ($conversationRawData == null) {

            return $this->redirectToRoute('homepage', [
                    
            ]);

        } else {

            $structuredPPData = $createSummaryService->createPPDataArray($_ENV['OPEN_AI_KEY'], $conversationRawData);

            $newPPStringId = $createPPService->create($structuredPPData);

            $presentationURL = $this->generateUrl('show_presentation', [

                'stringId'=> $newPPStringId,
                    
            ], UrlGeneratorInterface::ABSOLUTE_URL);

            $mailerService->mailAdmin("New presentation created from ai helper", "See <a href=".$presentationURL.">".$presentationURL."</a><br><p>Here is ai interview helper conversation :</p><pre>".json_encode($this->get('session')->get('ai_interview_helper_conversation'))."</pre>");

            return $this->redirectToRoute('show_presentation', [

                'stringId'=> $newPPStringId,
                'first-time-editor' => "true",
                    
            ]);

        }

        

       
    }



    

    /**
     * 
     * Deprecated : now summaries are done with an usal 4p (propon project presentation page)
     * 
    * @Route("/interview-ai-presentation-interview-helper/ajax-create-summary", name="ajax_ai_interview_create_summary")
    */
    public function ajaxInterviewCreateSummary(Request $request, DataCollectService $dataCollect, AICreatePPService $createSummaryService) {

        $structuredPPData = $createSummaryService->createPPDataArray($_ENV['OPEN_AI_KEY'], $this->get('session')->get('ai_interview_helper_conversation'));        

        return new JsonResponse(['summary' => $structuredPPData]);

    }















}

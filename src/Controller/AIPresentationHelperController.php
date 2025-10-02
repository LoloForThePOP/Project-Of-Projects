<?php

namespace App\Controller;

use OpenAI;

use App\Form\AIPPAdviceType;

use App\Service\MailerService;
use App\Service\CreatePPService;
use App\Service\DataCollectService;
use App\Service\AI\AICreatePPMaterialService;

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
     * Allow to give user a list of 10 project presentation advices (logged in user or not).
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
            $sender = $this->getParameter('app.email.noreply');
            $receiver = $this->getParameter('app.email.contact');

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
     * This method get the AI advice to present a project in session and display them in a twig template.
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
     * AI Propon project presentation assistant: asks some questions to user to finally automatically generate a structured Propon project presentation.
     * 
     * Here is the landing page frontend controller method (tasks are fractionned between some other controller methods see bellow)
     *
     * @Route("/ia-assistant-gratuit-entretien-projet/", name="ai_interview_helper_origin")
     */
    public function interviewOrigin(Request $request): Response
    {

        $context = $request->query->get('context'); //interview origin landing page can vary depending on context (ex: we already know user project goal (no need to ask him again) or we don't (need to ask him)) so we provide context to frontend twig template.

        $projectGoal = $this->get('session')->get('project_goal'); //If we already know project goal (ex: random unregistered user filled "what is your project goal?" homepage form)       

        $this->get('session')->set('ai_interview_helper_conversation', null); //Inits a session variable which stores user / AI conversation
        $this->get('session')->set('ai_interview_helper_count_interactions', 0); //Inits a session variable which stores user / AI conversation interactions count (when we have enough interaction we suggest user to make AI automatically present its project on Propon)

        return $this->render('ai_presentation_helper/interview/origin.html.twig', [
            'context' => $context,
            "projectGoal" => $projectGoal,
        ]);

    }



     /**
     * 
     * AI Propon project presentation assistant: ajax treatments (getting user message and sending AI message)
     * 
     * @Route("/ajax-ia-assistant-gratuit-entretien-projet", name="ajax_ai_interview_helper_origin")
     */
    public function ajaxInterviewOrigin(Request $request, DataCollectService $dataCollect, MailerService $mailerService) {
         
        if ($request->isXmlHttpRequest()) {

            $userMessage = $request->request->get('userMessage'); //Getting user message from frontend

            $ia = OpenAI::client($_ENV['OPEN_AI_KEY']); //Inits Open AI API

            //Case new conversation (start a conversation): we initiate the conversation
            if ($this->get('session')->get('ai_interview_helper_conversation') == null) {
                
                $messages =  [ //Prompt

                    ['role' => 'system', 'content' => "Vous êtes un coach expert en présentation de projet. Vous ne donnez aucun conseil pour réaliser le projet, vous donnez seulement de l'aide pour PRÉSENTER le projet à une ou plusieurs personnes (exemple: un maire, un jury d'investisseurs...). Vous demandez à l'utilisateur de clarifier l'objectif de son projet si besoin. Si besoin vous demandez des précisions à l'utilisateur. Vous savez poser les bonnes questions et vous aidez l'utilisateur à répondre à ces questions. Vous posez une seule et seulement une seule question à la fois."],

                    ['role' => 'user', 'content' => "Voici l'objectif de mon projet : ".$userMessage."."],

                ];
                
            } else {//Case conversation has already started before: we complete the array storing the conversation             

                $userAnswerAIRow = ['role' => 'user', 'content' => $userMessage]; //formatting user message in a array complying with Open AI conversation array format

                //Completing the formatted array storing the conversation
                $messages = $this->get('session')->get('ai_interview_helper_conversation');// Array containing the previously stored conversation (stored in a session variable)
                $messages[] = $userAnswerAIRow;// Adding user message to the stored conversation

            }

            /* Getting AI message as a result of user message */

            $response = $ia->chat()->create([
                'model' => 'gpt-3.5-turbo-0125',
                'messages' => $messages,
            ]);
            
            $response->toArray();
            $responseContent = $response['choices'][0]['message']['content'];//Actual AI answer text string


            //Storing AI message in an appropriately formatted array (as we've done above with user message)
            $assistantAnswerAIRow = ['role' => 'assistant', 'content' => $responseContent];

            $messages[] = $assistantAnswerAIRow;//Adding AI message to the array storing the entire conversation

            $this->get('session')->set('ai_interview_helper_conversation', $messages);//Storing this appropriately formatted array into the session variable so that we can reuse it when we continue conversation
            
            /* Counting & storing AI / user interactions during conversation */
            $abr="ai_interview_helper_count_interactions";//this long variable name is abreviated $abr
            $gabr=$this->get('session')->get($abr);//abreviation too

            $this->get('session')->set($abr, $gabr + 1);//updating AI / user interactions count

            if ($gabr == 3) {// if AI / user interactions are long enough: we collect conversation data to analyse it (product usage feedback)
                $dataCollectArray=[];
                $dataCollectArray["conversation"] = $messages;
                $dataCollect->save("ai_presentation_interview_helper", $dataCollectArray);

                //Email admin

                $mailerService->mailAdmin("New AI Presentation Interview Coach Usage", '<pre>'.json_encode($dataCollectArray, JSON_PRETTY_PRINT).'</pre>');

            }
    
            return new JsonResponse(['aiAnswer' => $responseContent]); //returns AI message to frontend
          
        }
   
    }



    /**
     * Creates an actual Propon Project Presantation Page given an AI / user conversation about user project 
     * 
     * 
    * @Route("/interview-ai-presentation-interview-helper/create-ppp", name="ai_create_ppp")
    */
    public function aiCreatePPP(AICreatePPMaterialService $createSummaryService, CreatePPService $createPPService, MailerService $mailerService) {

        // First we need the ai / user conversation stored in a session
        $conversationRawData = $this->get('session')->get('ai_interview_helper_conversation');

        if ($conversationRawData == null) {//if conversation is null user is redirected to homepage

            return $this->redirectToRoute('homepage', []);

        } else {// process of creating a Propon Project Presentation Page

            //The following service creates a PHP array containing formatted elements dscribing the project (project goal; project description; project title; etc).
            $structuredPPData = $createSummaryService->createPPDataArray($_ENV['OPEN_AI_KEY'], $conversationRawData);

            //Given the above mentionned PHP array representing a project presentation, the following service actually creates a Propon Project Presentation Page saved in DB. This service returns newly created project presentation slug (= stringId) so that we can redirected user to the created project presentation page.
            $newPPStringId = $createPPService->create($structuredPPData);

            //Generating URL of the newly created project presentation page
            $presentationURL = $this->generateUrl('show_presentation', [

                'stringId'=> $newPPStringId,
                    
            ], UrlGeneratorInterface::ABSOLUTE_URL);

            //Emailing admin that a project presentation page has just been created with AI helper
            $mailerService->mailAdmin("New presentation created from ai helper", "See <a href=".$presentationURL.">".$presentationURL."</a><br><p>Here is ai interview helper conversation :</p><pre>".json_encode($this->get('session')->get('ai_interview_helper_conversation'))."</pre>");

            //Redirecting user to their newly created 3P

            return $this->redirectToRoute('show_presentation', [

                'stringId'=> $newPPStringId,
                'first-time-editor' => "true",//flag meaning this is the first time sees his project presentation page (ex: we display a tutorial...)
                    
            ]);

        }

       
    }


}

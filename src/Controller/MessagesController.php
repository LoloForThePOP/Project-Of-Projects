<?php

namespace App\Controller;

use App\Entity\PPBase;
use App\Entity\Message;
use App\Entity\Conversation;
use App\Form\ContactWebsiteType;
use App\Form\PrivateMessageType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MessagesController extends AbstractController
{

    /**
     * Allow registered user to send a private message to a presentation creator
     * 
     * This action starts a new conversation
     * 
     * @Route("/projects/{stringId}/conversation/new/", name="new_pp_conversation")
     */
    public function newPPConversation(PPBase $presentation, Request $request): Response
    {

        $this->denyAccessUnlessGranted('ROLE_USER');
        
        $privateMessage = new Message();
        $form = $this->createForm(PrivateMessageType::class, $privateMessage);
        $form->handleRequest($request);

        
        if ($form->isSubmitted() && $form->isValid()) {

            $privateMessage
            
                ->setType("private_message")
                ->setAuthorUser($this->getUser());

            $conversation = new Conversation();

            $conversation 
                
                ->setPresentation($presentation)
                ->addMessage($privateMessage);            ;

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($privateMessage);
            $entityManager->persist($conversation);
            $entityManager->flush();
            
            $this->addFlash(
                'success',
                "✅ Votre Message a été envoyé"
            );

            //  to do : email notification to presentation creator

            return $this->redirectToRoute('show_presentation', [
                'stringId' => $presentation->getStringID(),
            ]);
        }



        return $this->render('project_presentation/conversations/new.html.twig', [

            'stringId' => $presentation->getStringId(),
            'form' => $form->createView(),
            
        ]);

    }


    /**
     * Allow to access contact website page
     * 
     * This action starts a new conversation
     * 
     * @Route("/contact-us", name="contact_website")
     */
    public function contactWebsite(Request $request): Response
    {
        
        $privateMessage = new Message();

        $form = 
        
            $this->createForm(

                ContactWebsiteType::class, 
                $privateMessage,
                array(

                    // Time protection
                    'antispam_time'     => true,
                    'antispam_time_min' => 4,
                    'antispam_time_max' => 3600,
                )
            );


        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            $privateMessage
            
                ->setType("contact_website");

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($privateMessage);
            $entityManager->flush();
            
            $this->addFlash(
                'success',
                "✅ Votre Message a été envoyé"
            );

            return $this->redirectToRoute('homepage', []);
        }

        return $this->render('/static/contact_us.html.twig', [

            'form' => $form->createView(),
            
        ]);

    }




}

<?php

namespace App\Controller;

use App\Entity\PPBase;
use App\Entity\Message;
use App\Entity\Conversation;
use App\Form\ContactWebsiteType;
use App\Form\PrivateMessageType;
use App\Repository\MessageRepository;
use Doctrine\ORM\Query\AST\BetweenExpression;
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

        $form = $this->createForm(PrivateMessageType::class, $privateMessage,
        array(

            // Time protection
            'antispam_time'     => true,
            'antispam_time_min' => 7,
            'antispam_time_max' => 3600,
        ));

        $form->handleRequest($request);

        
        if ($form->isSubmitted() && $form->isValid()) {

            $privateMessage
            
                ->setType("between_users")
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
     * Allow registered user to display / manage hiser conversations & messages list
     * 
     * @Route("/user/messages/", name="manage_user_messages")
     */
    public function manageConversations(MessageRepository $repo): Response
    {

        $this->denyAccessUnlessGranted('ROLE_USER');
        
        $userMessages = $repo->findBy(

            [
                'authorUser' => $this->getUser(),
                'isConsulted' => false,     
            
            ]

        );


        return $this->render('project_presentation/conversations/new.html.twig', [

            'userMessages' => $userMessages,
            
        ]);

    }


    /**
     * Allow any user to access contact website page
     * 
     * This action starts a new conversation
     * 
     * @Route("/contact-us/{context}/{item}/{identifier}", name="contact_website")
     */
    public function contactWebsite($context=null, $item=null, $identifier=null, Request $request): Response
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

            $context= $context.'-'.$item.'-'.$identifier;

            $privateMessage->setType("to_website");

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



    /**
     * 
     * Count registred user unread messages
     * 
     * @Route("/count-user-unread-messages", name="count_user_unread_messages")
     */
    public function countUserUnreadMessages(MessageRepository $repo): Response
    {

        $this->denyAccessUnlessGranted('ROLE_USER');

        $rows = $repo->findBy(

            [
                'authorUser' => $this->getUser(),
                'isConsulted' => false,     
            
            ]

        );
       
        return new Response(
            count($rows)
        );

    }




}

<?php

namespace App\Controller;

use App\Entity\PPBase;
use App\Entity\Message;
use App\Entity\Conversation;
use App\Form\ContactWebsiteType;
use App\Form\PrivateMessageType;
use App\Repository\MessageRepository;
use Doctrine\Common\Collections\Criteria;
use App\Repository\ConversationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MessagesController extends AbstractController
{

    /**
     * Allow registered user to send a private message to a presentation creator
     * 
     * This action starts a new conversation
     * 
     * @Route("/projects/{stringId}/conversation/new/{context}", name="new_pp_conversation")
     */
    public function newPPConversation(PPBase $presentation, $context=null, Request $request): Response
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
                
                ->setContext($context)
                ->addUser($this->getUser())
                ->addUser($presentation->getCreator())
                ->setContext($context)
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
     * @Route("/user/messages/", name="user_manage_messages")
     */
    public function manageConversations(Request $request, ConversationRepository $repo): Response
    {

        $this->denyAccessUnlessGranted('ROLE_USER');
          
        $newMessage = new Message();

        $form = $this->createForm(PrivateMessageType::class, $newMessage,
        array(

            // Time protection
            'antispam_time'     => true,
            'antispam_time_min' => 7,
            'antispam_time_max' => 3600,
        ));

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->getUser();

            $conversationId = $form->get('parentConversation')->getData();

            $conversation = $repo->findOneById($conversationId);

            if ($conversation->getUsers()->contains($user)) {
                    
                $newMessage ->setType("between_users")
                            ->setAuthorUser($user);

                $conversation->addMessage($newMessage);  
                
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($newMessage);
                $entityManager->flush();   

            }
            
            $this->addFlash(
                'success',
                "✅ Votre Message a été envoyé"
            );

            //  to do : email notification to message receiver

            return $this->redirectToRoute('user_manage_messages');
        }

        return $this->render('user/messages/manage_messages.html.twig', [

            'userConversations' => $repo->getConversations($this->getUser()),
            'form' => $form->createView(),
            
        ]);

    }

    
    /**
     * 
     * @Route("/user/messages/ajax-display-conversation", name="ajax_display_conversation")
     * 
     */
    public function ajaxDisplayConversation(Request $request, ConversationRepository $repo)
    {
        
        if ($request->isXmlHttpRequest()) {

            $this->denyAccessUnlessGranted('ROLE_USER');
            
            $idConversation = $request->request->get('idConversation');
            
            $conversation = $repo->findOneById($idConversation);
            
            if($conversation->getUsers()->contains($this->getUser())){

                $messages = $conversation->getMessages();
                
                $dataResponse = [

                    'html' => $this->renderView(
                        
                        'user/messages/_display_conversation.html.twig', 

                        [
                            'messages' => $messages,
                        ]
                    ),
                ];

                // Updating that conversation is consulted


                if ($messages->last()->getAuthorUser() != $this->getUser()) {

                    $conversation->setCacheItem('lastMessIsConsulted', true);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->flush();   

                }

            }

            else {

                $dataResponse = false;

            }

            //dump($dataResponse);

            return new JsonResponse($dataResponse);

        }

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

        $result = $repo->getUnreadMessages($this->getUser());

            return new Response(
                count($result)
            );

    }




}

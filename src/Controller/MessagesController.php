<?php

namespace App\Controller;

use App\Entity\PPBase;
use App\Entity\Message;
use App\Entity\Conversation;
use App\Form\ContactWebsiteType;
use App\Form\PrivateMessageType;
use Symfony\Component\Mime\Address;
use App\Repository\ConversationRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
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
    public function newPPConversation(PPBase $presentation, $context=null, Request $request, MailerInterface $mailer): Response
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


            $receiver=$presentation->getCreator();

            $conversation 
                
                ->setContext($context)
                ->addUser($this->getUser())
                ->addUser($receiver)
                ->setContext($context)
                ->setPresentation($presentation)
                ->addMessage($privateMessage);

            // updating receiver unread messages count

            $receiver->setDataItem("unreadMessagesCount", $receiver->getDataItem('unreadMessagesCount')+1);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($privateMessage);
            $entityManager->persist($conversation);
            $entityManager->flush();

            $email = (new TemplatedEmail())
                ->from($this->getParameter('app.mailer_email'))
                ->to(new Address($receiver->getEmail()))
                ->subject('Projet des Projets - Vous avez reçu un nouveau message')

                // path of the Twig template to render
                ->htmlTemplate('user/messages/email_got_new_message.html.twig')

                // pass variables (name => value) to the template
                ->context([
                    'projectGoal' => $presentation->getGoal(),
                ]);

            $mailer->send($email);
            
            $this->addFlash(
                'success',
                "✅ Votre message a été envoyé"
            );

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
    public function manageConversations(Request $request, ConversationRepository $repo, MailerInterface $mailer): Response
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
                
                // updating unread messages count

                foreach ($conversation->getUsers() as $receiver) {

                    if ($user != $receiver) {

                        $unreadMessagesCount= $receiver->getDataItem("unreadMessagesCount");

                        $receiver->setDataItem("unreadMessagesCount", $unreadMessagesCount+1);

                        $email = (new TemplatedEmail())
                            ->from($this->getParameter('app.mailer_email'))
                            ->to(new Address($receiver->getEmail()))
                            ->subject('Projet des Projets - Vous avez reçu un nouveau message')

                            // path of the Twig template to render
                            ->htmlTemplate('user/messages/email_got_new_message.html.twig')

                            // pass variables (name => value) to the template
                            ->context([
                                'projectGoal' => $conversation->getPresentation()->getGoal(),
                            ]);

                        $mailer->send($email);
                    }
                    
                }
                
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($newMessage);
                $entityManager->flush();   

            }
            
            $this->addFlash(
                'success',
                "✅ Votre message a été envoyé"
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
    public function ajaxDisplayConversation(Request $request, ConversationRepository $conversationsRepo)
    {
        
        if ($request->isXmlHttpRequest()) {

            $this->denyAccessUnlessGranted('ROLE_USER');
            
            $idConversation = $request->request->get('idConversation');
            
            $conversation = $conversationsRepo->findOneById($idConversation);

            $user= $this->getUser();
            
            if($conversation->getUsers()->contains($user)){

                $messages = $conversation->getMessages();
                
                $dataResponse = [

                    'html' => $this->renderView(
                        
                        'user/messages/_display_conversation.html.twig', 

                        [
                            'messages' => $messages,
                        ]
                    ),
                ];

                // Updating that now conversation is consulted
                // & updating read / unread messages states and count

                if ($messages->last()->getAuthorUser() != $user) {

                    $conversation->setCacheItem('lastMessIsConsulted', true);

                    $unreadMessagesCount = $user->getDataItem("unreadMessagesCount");
                    $newlyConsultedMessagesCount=0;

                    $conversationMessages = $conversation->getMessages();

                    foreach ($conversationMessages as $message) {

                        if ($message->getIsConsulted()==false) {
                            $message->setIsConsulted(true);
                            $newlyConsultedMessagesCount++;
                        }
                        
                    }

                    $user->setDataItem("unreadMessagesCount",$unreadMessagesCount-$newlyConsultedMessagesCount);

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
     * @Route("/contact-us/{context}/{item}/{identifier}", name="contact_website")
     */
    public function contactWebsite($context=null, $item=null, $identifier=null, Request $request, MailerInterface $mailer): Response
    {

        $form = 
        
            $this->createForm(

                ContactWebsiteType::class, 
                null,
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

            $sender = $form->get('authorEmail')->getData();

            $receiver = $this->getParameter('app.support_email');

            $email = (new TemplatedEmail())
                ->from($sender)
                ->to($receiver)
                ->subject('New Message from Contact Form')

                // path of the Twig template to render
                ->htmlTemplate('static/email_contact_form_to_website.html.twig')

                // pass variables (name => value) to the template
                ->context([
                    'context' => $context,
                    'messageContent' => $form->get('content')->getData(),
                    'sender' => $sender,
                ]);

            $mailer->send($email);
            
            $this->addFlash(
                'success',
                "✅ Votre message a été envoyé"
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
    /* public function countUserUnreadMessages(MessageRepository $repo): Response
    {

        $this->denyAccessUnlessGranted('ROLE_USER');

        $result = $repo->getUnreadMessages($this->getUser());

            return new Response(
                count($result)
            );

    } */




}

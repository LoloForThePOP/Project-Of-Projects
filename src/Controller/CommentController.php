<?php

namespace App\Controller;

use App\Entity\PPBase;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Service\CommentService;
use App\Repository\CommentRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentController extends AbstractController
{

    /**
     * Allow to ajax create a comment
     * 
     * @Route("/comment/ajax-create", name="ajax_create_comment")
     */
    public function ajaxCreateComment(Request $request, EntityManagerInterface $manager, CommentService $commentsService, NotificationService $notificationService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER'); //only connected users can comment

        if ($request->isXmlHttpRequest()) {

            session_write_close();

            $commentContent = $request->request->get('commentContent');

            $presentationId = $request->request->get('presentationId');

            $parentCommentId = $request->request->get('parentCommentId');

            $repliedCommentId = $request->request->get('repliedCommentId');

            $formTimeLoaded = $request->request->get('formTimeLoaded'); //antispam protection based on time
            $honeyPot = $request->request->get('hnyPt'); //antispam protection based on honey pot

            $presentation = $this->getDoctrine()->getRepository(PPBase::class)->findOneBy(['id' => $presentationId]);

            $comment = new Comment;

            
            $comment
                ->setUser($this->getUser())
                ->setProjectPresentation($presentation)
                ->setApproved(true)
                ->setContent($commentContent);

            $validation=$commentsService->validateComment($comment, $formTimeLoaded, $honeyPot);

            if (is_string($validation)) {
                return new JsonResponse(
                
                    [
                        'error' =>  $validation,
                    ],

                    Response::HTTP_BAD_REQUEST,
                );

            }

            $manager->persist($comment);

            $manager->flush();

        //when new comment is a reply
           if (!is_null($repliedCommentId)) {

            // We associate parent to the comment
            $parentComment = $this->getDoctrine()->getRepository(Comment::class)->findOneBy(['id' => $parentCommentId]);

            $comment->setParent($parentComment);
            
            $repliedComment = $this->getDoctrine()->getRepository(Comment::class)->findOneBy(['id' => $repliedCommentId]);

            // When we reply to a first degree child comment : we associate replied comment user so that we can show to whose child is replied to in frontend.

            //check if we reply to a first degree child
            if ($repliedCommentId !== $parentCommentId) {

                $comment->setRepliedUser($repliedComment->getUser());
            }

            //Notification to replied user if he / she's not presentation creator (because anyway we notify presentation creator)

/* 
            dump("new comment creator: ".$comment->getUser());
            dump("parent comment user: ".$parentComment->getUser());
            dump("replied comment user: ".$repliedComment->getUser());
            dump("pres creator: ".$presentation->getCreator()); */

            if ($repliedComment->getUser() !== $presentation->getCreator()) {

                $notificationParams=[
                    "presentation" => $presentation,
                    "repliedComment" => $parentComment,
                    "answer" => $comment,
                ];

                $notificationService->process('comment', 'repliedComment', $notificationParams);
            }

        }

        //Notification to project presentation creator

        $notificationParams=[
            "presentation" => $presentation,
            "comment" => $comment,
        ];

        $notificationService->process('comment', 'projectPresentationCommented', $notificationParams);

        // End of Notification to project presentation creator


        $newCommentEditionUrl = $this->generateUrl('update_comment', array('id' => $comment->getId()));

        $responseData = [
            'newCommentEditionUrl' =>  $newCommentEditionUrl,
        ];

        
        return  new JsonResponse($responseData);

        }

        return  new JsonResponse(false);

        
    }

    /**
     * Allow to update a comment
     * 
     * @Route("/comment/update/{id}", name="update_comment")
     */
    public function updateComment(Request $request, EntityManagerInterface $manager, Comment $comment):response
    {

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        $this->denyAccessUnlessGranted('update', $comment);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager->flush();

            return $this->redirectToRoute('show_presentation', [
                'stringId' => $comment->getProjectPresentation()->getStringId(),
                '_fragment' => 'comments-struct-container',
            ]);

        }

        return $this->render('comment/edit.html.twig', [
            'form' => $form->createView(),
            'stringId' => $comment->getProjectPresentation()->getStringId(),
        ]);
        
    }


    
    
    /**
     * Allow to ajax remove a comment
     * 
     * @Route("/comments/remove/", name="ajax_remove_comment")
     * 
     * @return Response
     */
   public function delete(Request $request, EntityManagerInterface $manager, CommentRepository $repo){

    if ($request->isXmlHttpRequest()) {

        $commentId = $request->request->get('commentId');

        $comment = $repo->findOneById($commentId);

        $this->denyAccessUnlessGranted('delete', $comment);

        $feedbackCode = false;

        $manager->remove($comment);
            
        $manager->flush();

        $feedbackCode = true;

        $dataResponse = [
            'feedbackCode' => $feedbackCode,
        ];

        return new JsonResponse($dataResponse);

    }
}






}

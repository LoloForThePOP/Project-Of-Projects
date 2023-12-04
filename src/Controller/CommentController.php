<?php

namespace App\Controller;

use App\Entity\PPBase;
use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Service\CommentService;
use App\Service\NotificationService;
use App\Repository\CommentRepository;
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

            
            $commentedEntityType = $request->request->get('commentedEntityType');
            
            $commentedEntityId = $request->request->get('commentedEntityId');
            
            $commentContent = $request->request->get('commentContent');

            $parentCommentId = $request->request->get('parentCommentId');

            $repliedCommentId = $request->request->get('repliedCommentId');

            $formTimeLoaded = $request->request->get('formTimeLoaded'); //antispam protection based on time
            $honeyPot = $request->request->get('hnyPt'); //antispam protection based on honey pot

            $comment = new Comment;

            switch ($commentedEntityType) {

                case 'projectPresentation':
                    $presentation = $this->getDoctrine()->getRepository(PPBase::class)->findOneBy(['id' => $commentedEntityId]);
                    $comment->setProjectPresentation($presentation);
                    break;

                case 'article':
                    $article = $this->getDoctrine()->getRepository(Article::class)->findOneBy(['id' => $commentedEntityId]);
                    $comment->setArticle($article);
                    break;
                
                default:
                    # code...
                    break;

            }

            $comment
                ->setUser($this->getUser())
                ->setApproved(true)
                ->setContent($commentContent);

            $validation = $commentsService->validateComment($comment, $formTimeLoaded, $honeyPot);

            if (is_string($validation)) {
                return new JsonResponse(
                
                    [
                        'error' =>  $validation,
                    ],

                    Response::HTTP_BAD_REQUEST,
                );

            }

        //when new comment is a reply
        if (!is_null($repliedCommentId)) {

            // We associate parent to the comment
            $parentComment = $this->getDoctrine()->getRepository(Comment::class)->findOneBy(['id' => $parentCommentId]);

            $comment->setParent($parentComment);
            
            $repliedComment = $this->getDoctrine()->getRepository(Comment::class)->findOneBy(['id' => $repliedCommentId]);

            // When we reply to a first degree child comment : we associate this replied comment user author to the reply comment so that we can show to whose child the new comment is replied to in frontend.

            //So we check if we reply to a first degree child
            if ($repliedCommentId !== $parentCommentId) {

                $comment->setRepliedUser($repliedComment->getUser());

            }

            //Notification management concerning comment replies

            switch ($commentedEntityType) {

                case 'projectPresentation':

                    $notificationParams = [
                        "presentation" => $presentation,
                        "repliedComment" => $parentComment,
                        "answer" => $comment,
                    ];
        
                    $notificationService->process('comment', 'projectPresentationRepliedComment', $notificationParams);
                    break;

                case 'article':
                   
                    break;
                
                default:
                    # code...
                    break;

            }

           

        }

        //Notification management other than replies

        switch ($commentedEntityType) {

            case 'projectPresentation':

                //Notification to project presentation creator

                $notificationParams=[
                    "presentation" => $presentation,
                    "comment" => $comment,
                ];

                $notificationService->process('comment', 'projectPresentationCommented', $notificationParams);
                break;

            case 'article':

                //Notification to project presentation creator

                $notificationParams=[
                    "article" => $article,
                    "comment" => $comment,
                ];

                $notificationService->process('comment', 'articleCommented', $notificationParams);
                break;
               
                break;
            
            default:
                # code...
                break;

        }

        
        $manager->persist($comment);

        $manager->flush();
        
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

        $goBackUrl = null;

        switch ($comment->getCommentedEntityType()) {

            case 'projectPresentation':

                $goBackUrl = $this->generateUrl('show_presentation', [
                    'stringId' => $comment->getProjectPresentation()->getStringId(),
                    '_fragment' => 'comments-struct-container',
                ]);
                break;

            case 'article':
                $goBackUrl = $this->generateUrl('show_article', [
                    'slug' => $comment->getArticle()->getSlug(),
                    '_fragment' => 'comments-struct-container',
                ]);
                break;
            
            default:
                throw new \Exception("Unknown Go Back Button Route");
                
        }


        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        $this->denyAccessUnlessGranted('update', $comment);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager->flush();

            switch ($comment->getCommentedEntityType()) {
                case 'projectPresentation':
                    return $this->redirectToRoute('show_presentation', [
                        'stringId' => $comment->getProjectPresentation()->getStringId(),
                        '_fragment' => 'comments-struct-container',
                    ]);
                    break;
                case 'article':
                    return $this->redirectToRoute('show_article', [
                        'slug' => $comment->getArticle()->getSlug(),
                        '_fragment' => 'comments-struct-container',
                    ]);
                    break;
                
                default:
                    # code...
                    break;
            }


        }

        return $this->render('comment/edit.html.twig', [
            'form' => $form->createView(),
            'goBackUrl' => $goBackUrl,
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

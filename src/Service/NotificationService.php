<?php

namespace App\Service;

use App\Entity\User;
use Twig\Environment;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


/*

Note on comments : comments and replies to comments follow a similar structure as youtube comments structure (as of year 2023).

Vocabulary : 

- cmt 1
- cmt 2 (has childs, so is called a parent comment)
    - cmt 2.1 (is called an answered comment, or replied comment, because cmt 2.2 below answers specifically to it)
    - cmt 2.2 @cmt 2.1
    - cmt 2.3
- cmt 2

When a comment is replied, potentially we can send a notification up to three user : commented page author (ex: the commented article author), comment thread initial author (= parent comment author, see above), replied comment author (= someone participate to a thread and someone answers to a comment).

*/

class NotificationService {


    protected $em;
    protected $twig;
    protected $mailerService;
    protected $router; //to generate urls



    public function __construct(EntityManagerInterface $em, Environment $twig, MailerService $mailerService, UrlGeneratorInterface $router)
    {
      
        $this->em = $em;
        $this->twig = $twig;
        $this->mailerService = $mailerService;
        $this->router = $router;

    }

    public function process($notificationObject, $notificationType, $notificationParams){

        $parentComment = $notificationParams["parentComment"];
        $repliedSiblingComment = $notificationParams["repliedSiblingComment"];
        $newComment = $notificationParams["newComment"];

        switch ($notificationObject) {

            case 'comment':

                switch ($notificationType) {

                    case 'projectPresentation':

                        $presentation = $notificationParams["projectPresentation"];

                        $this->commentProjectPresentation($presentation, $parentComment, $repliedSiblingComment, $newComment);

                        break;

                    case 'article':

                        $article = $notificationParams["article"];

                        $this->commentArticle($article, $parentComment, $repliedSiblingComment, $newComment);

                        break;                    

                    case 'news':

                        $news = $notificationParams["news"];

                        $this->commentNews($news, $parentComment, $repliedSiblingComment, $newComment);

                        break;                    
            
                    default:
                        throw new \Exception("Unsupported notification type");
                        break;

                }

                break;
                
            case 'news':

                switch ($notificationType) {

                    case 'projectPresentationCreation':

                        $presentation = $notificationParams["presentation"];

                        $this->newProjectNews($presentation);

                        break;
                    
                    default:
                        throw new \Exception("Unsupported notification type");
                        break;
                }

                break;

            default:
                throw new \Exception("Unsupported notification object");
                break;

        }

    }

    private function commentProjectPresentation($presentation, $parentComment, $repliedSiblingComment, $newComment){

        $commentedPageUrl = $this->router->generate('show_presentation', array('stringId' => $presentation->getStringId()), UrlGeneratorInterface::ABSOLUTE_URL);

        if ($presentation->getCreator() !== $newComment->getUser()) {//if new comment author is different from project presenter, we notify project presenter.

            $notificationReceiver = $presentation->getCreator();

            $notificationTitle = "Propon - Nouveau commentaire reçu";

            $emailContentFilePath = 'email_notifications/page_commented.html.twig';

            $emailContentParameters = [

                'pageType' => "projectPresentation",
                'presentation' => $presentation,
                'newComment' => $newComment,
                'commentedPageUrl' => $commentedPageUrl,
            ];

            $this->sendOrLogNotification($notificationReceiver, "comment", "projectPresentationCommented", $notificationTitle, $emailContentFilePath, $emailContentParameters);

        }


        if($parentComment !== null){ // if new comment is a reply, maybe we additionaly notify parent comment thread author and potential replied siblings comment author.

            if ($parentComment->getUser() !== $presentation->getCreator() && $parentComment->getUser() !== $newComment->getUser()) { 

                $notificationReceiver = $parentComment->getUser();

                $notificationTitle = "Propon - Nouveau commentaire reçu sur une discussion";
    
                $emailContentFilePath = 'email_notifications/replied_comment_thread.html.twig';
    
                $emailContentParameters = [
    
                    'pageType' => "projectPresentation",
                    'presentation' => $presentation,
                    'initialCommentInThread' => $parentComment,
                    'newComment' => $newComment,
                    'commentedPageUrl' => $commentedPageUrl,
                ];
    
                $this->sendOrLogNotification($notificationReceiver, "comment", "projectPresentationCommented", $notificationTitle, $emailContentFilePath, $emailContentParameters);
                
            }

        }

        if($repliedSiblingComment !== null){ // case when we speciffically reply to a child comment (and not just an initial thread comment)

            if ($repliedSiblingComment->getUser() !== $presentation->getCreator() && $parentComment->getUser() !== $repliedSiblingComment->getUser()) {

                $notificationReceiver = $repliedSiblingComment->getUser();

                $notificationTitle = "Propon - Nouveau commentaire reçu";
    
                $emailContentFilePath = 'email_notifications/replied_comment.html.twig';
    
                $emailContentParameters = [
    
                    'pageType' => "projectPresentation",
                    'presentation' => $presentation,
                    'repliedSiblingComment' => $repliedSiblingComment,
                    'newComment' => $newComment,
                    'commentedPageUrl' => $commentedPageUrl,
                ];
    
                $this->sendOrLogNotification($notificationReceiver, "comment", "projectPresentationCommented", $notificationTitle, $emailContentFilePath, $emailContentParameters);


            }

        }

        return;

    }

    private function commentArticle($article, $parentComment, $repliedSiblingComment, $newComment){

        $commentedPageUrl = $this->router->generate('show_article', array('slug' => $article->getSlug()), UrlGeneratorInterface::ABSOLUTE_URL);

        if ($article->getAuthor() !== $newComment->getUser()) {//if new comment author is different from article author, we notify article author.

            $notificationReceiver = $article->getAuthor();

            $notificationTitle = "Propon - Nouveau commentaire reçu";

            $emailContentFilePath = 'email_notifications/page_commented.html.twig';

            $emailContentParameters = [

                'pageType' => "article",
                'article' => $article,
                'newComment' => $newComment,
                'commentedPageUrl' => $commentedPageUrl,
            ];

            $this->sendOrLogNotification($notificationReceiver, "comment", "articleCommented", $notificationTitle, $emailContentFilePath, $emailContentParameters);

        }


        if($parentComment !== null){ // if new comment is a reply, maybe we additionaly notify parent comment thread author and potential replied siblings comment author.

            if ($parentComment->getUser() !== $article->getAuthor() && $parentComment->getUser() !== $newComment->getUser()) { 

                $notificationReceiver = $parentComment->getUser();

                $notificationTitle = "Propon - Nouveau commentaire reçu sur une discussion";
    
                $emailContentFilePath = 'email_notifications/replied_comment_thread.html.twig';
    
                $emailContentParameters = [
    
                    'pageType' => "article",
                    'article' => $article,
                    'initialCommentInThread' => $parentComment,
                    'newComment' => $newComment,
                    'commentedPageUrl' => $commentedPageUrl,
                ];
    
                $this->sendOrLogNotification($notificationReceiver, "comment", "articleCommented", $notificationTitle, $emailContentFilePath, $emailContentParameters);
                
            }

        }

        if($repliedSiblingComment !== null){ // case when we speciffically reply to a child comment (and not just an initial thread comment)

            if ($repliedSiblingComment->getUser() !== $article->getAuthor() && $parentComment->getUser() !== $repliedSiblingComment->getUser()) {

                $notificationReceiver = $repliedSiblingComment->getUser();

                $notificationTitle = "Propon - Nouveau commentaire reçu";
    
                $emailContentFilePath = 'email_notifications/replied_comment.html.twig';
    
                $emailContentParameters = [
    
                    'pageType' => "article",
                    'article' => $article,
                    'repliedSiblingComment' => $repliedSiblingComment,
                    'newComment' => $newComment,
                    'commentedPageUrl' => $commentedPageUrl,
                ];
    
                $this->sendOrLogNotification($notificationReceiver, "comment", "articleCommented", $notificationTitle, $emailContentFilePath, $emailContentParameters);

            }

        }

        return;

    }

    private function commentNews($news, $parentComment, $repliedSiblingComment, $newComment){

        // !!! As of 2023 we ONLY CONSIDER NEWS FROM PROJECT PRESENTATION
        $commentedPageUrl = $this->router->generate('show_presentation', array('stringId' => $news->getProject()->getStringId()), UrlGeneratorInterface::ABSOLUTE_URL);

        // !!! As of 2023 $news->getAuthor() is the project prensenter
        if ($news->getAuthor() !== $newComment->getUser()) {//if new comment author is different from project presenter, we notify project presenter.

            $notificationReceiver = $news->getAuthor();

            $notificationTitle = "Propon - Nouveau commentaire reçu";

            $emailContentFilePath = 'email_notifications/page_commented.html.twig';

            $emailContentParameters = [

                'pageType' => "news",
                'news' => $news,
                'newComment' => $newComment,
                'commentedPageUrl' => $commentedPageUrl,
            ];

            $this->sendOrLogNotification($notificationReceiver, "comment", "newsCommented", $notificationTitle, $emailContentFilePath, $emailContentParameters);

        }


        if($parentComment !== null){ // if new comment is a reply, maybe we additionaly notify parent comment thread author and potential replied siblings comment author.

            if ($parentComment->getUser() !== $news->getAuthor() && $parentComment->getUser() !== $newComment->getUser()) { 

                $notificationReceiver = $parentComment->getUser();

                $notificationTitle = "Propon - Nouveau commentaire reçu sur une discussion";
    
                $emailContentFilePath = 'email_notifications/replied_comment_thread.html.twig';
    
                $emailContentParameters = [
    
                    'pageType' => "news",
                    'news' => $news,
                    'initialCommentInThread' => $parentComment,
                    'newComment' => $newComment,
                    'commentedPageUrl' => $commentedPageUrl,
                ];
    
                $this->sendOrLogNotification($notificationReceiver, "comment", "newsCommented", $notificationTitle, $emailContentFilePath, $emailContentParameters);
                
            }

        }

        if($repliedSiblingComment !== null){ // case when we speciffically reply to a child comment (and not just an initial thread comment)

            if ($repliedSiblingComment->getUser() !== $news->getAuthor() && $parentComment->getUser() !== $repliedSiblingComment->getUser()) {

                $notificationReceiver = $repliedSiblingComment->getUser();

                $notificationTitle = "Propon - Nouveau commentaire reçu";
    
                $emailContentFilePath = 'email_notifications/replied_comment.html.twig';
    
                $emailContentParameters = [
    
                    'pageType' => "news",
                    'news' => $news,
                    'repliedSiblingComment' => $repliedSiblingComment,
                    'newComment' => $newComment,
                    'commentedPageUrl' => $commentedPageUrl,
                ];
    
                $this->sendOrLogNotification($notificationReceiver, "comment", "newsCommented", $notificationTitle, $emailContentFilePath, $emailContentParameters);

            }




        }

        return;

    }




















    
    private function newProjectNews($presentation){
        

        $notificationTitleEnding ="";

        if (!empty($presentation->getTitle())){
            $notificationTitleEnding = mb_strimwidth($presentation->getTitle(), 0, 20, "…");
        }else{
            $notificationTitleEnding = mb_strimwidth($presentation->getGoal(), 0, 20, "…");
        }

        
        $notificationTitle = 'Des nouvelles du Projet «'.$notificationTitleEnding.'»';

        $presentationFollowers = $presentation->getFollowers();

        $emailContentFilePath = 'email_notifications/news_creation.html.twig';
        
        $emailContentParameters = [

            'presentation' => $presentation,

        ];

        foreach ($presentationFollowers as $presentationFollower) {
                       
            $this->sendOrLogNotification($presentationFollower, "news", "projectPresentationCreation", $notificationTitle, $emailContentFilePath, $emailContentParameters);

        }

    }


    private function sendOrLogNotification(User $userReceiver, $notificationObject, $notificationType, $notificationTitle, $emailContentFilePath, $emailContentParameters){


        $lastTimeEmailNotification = $userReceiver->getDataItem('lastEmailNotificationDate');


        //The if condition bellow is to finish if notifications are frequent.
        //if we have sent a notification a short time ago, we register this notification in notification journal
        if (time() - $lastTimeEmailNotification < 10) {

            $notificationRow = [
                "createdAt" => new \DateTimeImmutable('now'),
                "object" => $notificationObject,
                "type" => $notificationType,
                "summary" =>"To Do",
            ];

            $notificationJournalBufferUpdate = $userReceiver->getDataItem('notificationJournalBuffer');
            $notificationJournalBufferUpdate[] = $notificationRow;

            $userReceiver->setDataItem('notificationJournalBuffer', $notificationJournalBufferUpdate);

            $this->em->flush();

        } else {//we send an email and purge notification journal buffer

            $sender='mailer@propon.org';
            $senderTitle='Propon';

            $this->mailerService->send($sender, $senderTitle, $userReceiver->getEmail(), $notificationTitle, $emailContentFilePath, $emailContentParameters);

        }

    }




    











}

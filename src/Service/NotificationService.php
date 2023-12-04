<?php

namespace App\Service;

use App\Entity\User;
use Twig\Environment;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;


class NotificationService {


    protected $em;
    protected $twig;
    protected $mailerService;



    public function __construct(EntityManagerInterface $em, Environment $twig, MailerService $mailerService)
    {
      
        $this->em = $em;
        $this->twig = $twig;
        $this->mailerService = $mailerService;

    }

    public function process($notificationObject, $notificationType, $notificationParams){

        switch ($notificationObject) {

            case 'comment':

                switch ($notificationType) {

                    case 'projectPresentationCommented':

                        $presentation = $notificationParams["presentation"];
                        $comment = $notificationParams["comment"];

                        $this->newCommentProjectPresentation($presentation, $comment);

                        break;

                    case 'projectPresentationRepliedComment':

                        $repliedComment = $notificationParams["repliedComment"];
                        $presentation = $notificationParams["presentation"];
                        $answer = $notificationParams["answer"];

                        $this->repliedCommentProjectPresentation($presentation, $repliedComment, $answer);

                        break;

                    case 'articleCommented':

                        $article = $notificationParams["article"];
                        $comment = $notificationParams["comment"];

                        $this->newCommentArticle($article, $comment);

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

    private function newCommentProjectPresentation($presentation, $comment){

        if ($presentation->getCreator()==$comment->getUser()) {//if project presenter comments its own page, we do not notify him.
            return;
        }

        $notificationReceiver = $presentation->getCreator();

        $notificationTitle = "Propon  - Nouveau commentaire reçu";

        $emailContentFilePath = 'email_notifications/project_presentation_commented.html.twig';

        $emailContentParameters = [

            'presentation' => $presentation,
            'comment' => $comment,
        ];

        $this->sendOrLogNotification($notificationReceiver, "comment", "projectPresentationCommented", $notificationTitle, $emailContentFilePath, $emailContentParameters);

    }



    private function newCommentArticle($article, $comment){

        if ($article->getAuthor()==$comment->getUser()) {//if article author comments its own article, we do not notify him.
            return;
        }

        $notificationReceiver = $article->getAuthor();

        $notificationTitle = "Propon  - Nouveau commentaire reçu";

        $emailContentFilePath = 'email_notifications/article_commented.html.twig';

        $emailContentParameters = [

            'article' => $article,
            'comment' => $comment,
        ];

        $this->sendOrLogNotification($notificationReceiver, "comment", "articleCommented", $notificationTitle, $emailContentFilePath, $emailContentParameters);

    }




    private function repliedCommentProjectPresentation($presentation, $repliedComment, $answer){
        
        //when comment replier is project presenter, we do not have to notice him twice (he is noticed by default)
        if ($repliedComment->getUser() == $presentation->getCreator()) {

            return;
            
        }

        //when comment replier author is the same as parent comment author, we don't have to notice him
/*         if ($repliedComment->getUser() == $repliedComment->getParent()->getUser()) {

            return;
            
        } */

        $emailContentFilePath = 'email_notifications/replied_comment.html.twig';

        $emailContentParameters = [
            'presentation' => $presentation,
            'repliedComment' => $repliedComment,
            'answer' => $answer,
        ];

        $notificationReceiver = $repliedComment->getUser();
    
        $notificationTitle = "Propon - Nouveau commentaire en réponse";

        $this->sendOrLogNotification($notificationReceiver, "comment", "projectPresentationRepliedComment", $notificationTitle, $emailContentFilePath, $emailContentParameters);

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

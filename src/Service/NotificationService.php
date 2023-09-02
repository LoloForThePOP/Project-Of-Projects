<?php

namespace App\Service;

use App\Entity\User;
use Twig\Environment;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;


class NotificationService {

    /*
    *
    * Categories of notifications
    *
    * Cat 0 : compulsory (highly important)
    * Cat 1 :
    */

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

                        $notificationReceiver = $presentation->getCreator();

                        $notificationTitle = "Propon  - Nouveau commentaire reçu";
            
                        $emailContentFilePath = 'email_notifications/project_presentation_commented.html.twig';

                        $emailContentParameters = [
                            'presentation' => $presentation,
                            'comment' => $comment,
                        ];

                        break;

                    case 'repliedComment':

                        $repliedComment = $notificationParams["repliedComment"];

                        $emailContentFilePath = 'email_notifications/replied_comment.html.twig';

                        $emailContentParameters = [
                            'presentation' => $notificationParams["presentation"],
                            'repliedComment' => $repliedComment,
                            'answer' => $notificationParams["answer"],
                        ];

                        $notificationReceiver = $repliedComment->getUser();
                    
                        $notificationTitle = "Propon - Nouveau commentaire en réponse";

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

        $lastTimeEmailNotification = $notificationReceiver->getDataItem('lastEmailNotificationDate', time());

        //The if condition bellow is to finish if notifications are frequent.
        //if we have sent a notification a short time ago, we register this notification in notification journal
        if ($lastTimeEmailNotification - time() < 3600) {
            
            $notificationRow = [
                "createdAt" => new \DateTimeImmutable('now'),
                "object" => $notificationObject,
                "type" => $notificationType,
                "summary" =>"To Do",
            ];

            $notificationJournalBufferUpdate = $notificationReceiver->getDataItem('notificationJournalBuffer');
            $notificationJournalBufferUpdate[] = $notificationRow;

            $notificationReceiver->setDataItem('notificationJournalBuffer', $notificationJournalBufferUpdate);

            $this->em->flush();

        } else {//we send an email and purge notification journal buffer

            $sender='mailer@propon.org';
            $senderTitle='Propon';

            $this->mailerService->send($sender, $senderTitle, $notificationReceiver->getEmail(), $notificationTitle, $emailContentFilePath, $emailContentParameters);

        }

    }


}

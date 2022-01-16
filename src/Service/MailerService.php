<?php

namespace App\Service;

use Symfony\Component\Mime\Email;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;


class MailerService {

    protected $mailer;

    public function __construct(MailerInterface $mailer)
    {
        
        $this->mailer = $mailer;

    }


    /**
    * Allow to send an email
    * $content is a twig template file name with directory, or an html string.
    * If $content is a twig template, we can pass an array of additional $emailParameters
    */

    public function send($sender, $senderTitle, $receiver, $subject, $content, $emailParameters=false){

        $templatedInstance = false;

        if (substr($content, -4) == 'twig') {//$content is a twig template address

            $email = (new templatedEmail());

            $templatedInstance = true;
             
        }

        else{//$content is an html string

            $email = (new Email());

        }

        $email

            ->from (new Address($sender, $senderTitle))
            ->to(new Address($receiver))
            ->subject($subject);

        if ($templatedInstance) { // in this case $content is path of the Twig template to render
            
            $email->htmlTemplate($content);

            if ($emailParameters) {

                $email-> context($emailParameters);
            }
            
        }

        else {
            $email->html($content);
        }

        $this->mailer->send($email);

        return true;

    }


}

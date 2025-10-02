<?php

namespace App\Controller;

use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * Manage a reusable feedback form to get users feedback
 */
class FeedbackController extends AbstractController
{

    /** 
     * 
     * Allow to get results from a feedback form via an ajax request & email them to admin
     * Reusable feedback form template name: _macro_feedback_form.html.twig
     *  
     * @Route("/ajax-feedback", name="ajax_feedback_post") 
     * 
    */
    public function ajaxFeedbackPost(Request $request, MailerInterface $mailer){

        if ($request->isXmlHttpRequest()) {

            session_write_close();

            $result=[]; //Feedback form data are stored in an array

            //Feeding array with feedback form data

            $result["feedbackContext"] = $request->request->get('feedbackContext');

            $result["overallRating"] = $request->request->get('overallRating');

            $result["userSuggestions"] = $request->request->get('userSuggestions');

            /* If user is logged in we get her email adress in case we'd like to contact her and ask more questions */

            $user=$this->getUser();

            if ($user) {
                $result['email'] = $user->getEmail();
            }

            /* Email feedback to admin */

            $sender = $this->getParameter('app.email.noreply');
            $receiver = $this->getParameter('app.email.contact');

            $email = (new Email())
                ->from($sender)
                ->to($receiver)
                ->subject('New user Feedback')
                ->html('<pre>'.json_encode($result, JSON_PRETTY_PRINT).'</pre>');

            $mailer->send($email);

            return  new JsonResponse(true);

        }

        return  new JsonResponse(false);

    }


}

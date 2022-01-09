<?php

namespace App\Controller;

use Stripe\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WebhookController extends AbstractController
{
  
    /**
    * @Route("/webhooks/stripe", name="webhook_stripe")
    */
    public function stripeWebhookAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            throw new \Exception('Bad JSON body from Stripe!');
        }

        $eventId = $data['id'];

        $stripeEvent = \Stripe\Event::retrieve($eventId);

        
        
        return new Response('baaaaaa');
    }



}

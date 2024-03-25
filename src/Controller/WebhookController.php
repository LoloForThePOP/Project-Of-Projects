<?php

namespace App\Controller;

use Stripe\Event;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\StripeClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WebhookController extends AbstractController
{


  /**
  * @Route("/webhooks/stripe", name="webhook_stripe")
  */
  public function stripeWebhookAction()
  {

    $stripe = new StripeClient($_ENV[strtoupper($_ENV['APP_ENV']).'_STRIPE_SECRET_KEY']);

    // This is your Stripe CLI webhook secret for testing your endpoint locally.
    $endpoint_secret = $_ENV[strtoupper($_ENV['APP_ENV']).'_STRIPE_WEBHOOKS_SECRET_KEY'];

    $payload = @file_get_contents('php://input');
    $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
    $event = null;

    try {
      $event = Webhook::constructEvent(
        $payload, $sig_header, $endpoint_secret
      );
    } catch(\UnexpectedValueException $e) {
      // Invalid payload
      return new Response(400);
      exit();
    } catch(\Stripe\Exception\SignatureVerificationException $e) {
      // Invalid signature
      return new Response(400);
      exit();
    }

    switch ($event->type) {

      case 'payment_intent.succeeded':
        //$paymentIntent = $event->data->object;

        return new Response(200);
        $paymentIntent = $event->data->object;        

      // ... handle other event types
      default:
        echo 'Received unknown event type ' . $event->type;
    }


    return new Response(200);


  }



 

}

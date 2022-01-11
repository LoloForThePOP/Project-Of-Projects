<?php

namespace App\Controller;

use Stripe\Event;
use Stripe\Stripe;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WebhookController extends AbstractController
{

            
    public function print_log($val) {
        return file_put_contents('php://stderr', print_r($val, TRUE));
    }
  
    /**
    * @Route("/webhooks/stripe", name="webhook_stripe")
    */
    public function stripeWebhookAction()
    {
        // Set your secret key. Remember to switch to your live secret key in production.
        // See your keys here: https://dashboard.stripe.com/apikeys
        \Stripe\Stripe::setApiKey(

            'sk_test_51KEEIYCW4N9Dp51Q47GlgI7tvVN4wEiATXozyMoPXX31E7o9P2PbpXJeUw7cUoWR5hWsj29cyUOzdnPXlz3ymajt002wA4uGeJ'
        );

        $endpoint_secret = 'whsec_ja7hO18L2oamnRR5wqRWdui84y53ELtK';

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
$event = null;

try {
  $event = \Stripe\Webhook::constructEvent(
    $payload, $sig_header, $endpoint_secret
  );
} catch(\UnexpectedValueException $e) {
  // Invalid payload
  http_response_code(400);
  exit();
} catch(\Stripe\Exception\SignatureVerificationException $e) {
  // Invalid signature
  http_response_code(400);
  exit();
}

$this->print_log("Passed signature verification!");
http_response_code(200);


    }
        



}

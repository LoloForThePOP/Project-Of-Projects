<?php

namespace App\Controller;

use Stripe\Event;
use Stripe\Stripe;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WebhookController extends AbstractController
{

  protected $logger;

  public function __construct(LoggerInterface $logger){
        
    $this->logger = $logger;

  }
            
  protected function print_log($val) {
    return file_put_contents('php://stderr', print_r($val, TRUE));
  }

  protected function fulfill_order($session) {
    // TODO: fill me in
    $this->logger->info("Fulfilling order...");
    //$this->logger->info($session);
  }

  /**
  * @Route("/webhooks/stripe", name="webhook_stripe")
  */
  public function stripeWebhookAction()
  {

    \Stripe\Stripe::setApiKey(

      'sk_test_51KEEIYCW4N9Dp51Q47GlgI7tvVN4wEiATXozyMoPXX31E7o9P2PbpXJeUw7cUoWR5hWsj29cyUOzdnPXlz3ymajt002wA4uGeJ'

    );

    $endpoint_secret = $_ENV[strtoupper($_ENV['APP_ENV']).'_STRIPE_WEBHOOKS_SECRET_KEY'];

    $this->logger->info($endpoint_secret);
      
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
    
    $this->logger->info("Passed signature verification!");

    // Handle the payment_intent.succeeded event
    if ($event->type == 'payment_intent.succeeded') {
      $session = $event->data->object;

      // Fulfill the purchase...
      $this->fulfill_order($session);
    }

    return new Response();

  }
        



}

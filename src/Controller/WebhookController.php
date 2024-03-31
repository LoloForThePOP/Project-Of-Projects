<?php

namespace App\Controller;

use Stripe\Event;
use Stripe\Stripe;
use Stripe\Webhook;
use App\Entity\PPBase;
use App\Entity\Purchase;
use Stripe\StripeClient;
use App\Service\MailerService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WebhookController extends AbstractController
{


  /**
  * @Route("/webhooks/stripe", name="webhook_stripe")
  */
  public function stripeWebhookAction(MailerService $mailerService)
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
        
        $purchase = $this->getDoctrine()->getRepository(Purchase::class)->findOneBy(['token' => $event->data->object->id]);
        $purchase->setStatus("PAID");

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        //sending an email to donation receiver

        $concernedPresentationId = $purchase->getContentItem("projectId");
        $concernedPresentation =  $this->getDoctrine()->getRepository(PPBase::class)->findOneBy(['id' => $concernedPresentationId]);

        $receiverEmail = $concernedPresentation->getCreator()->getEmail();

        $sender = $this->getParameter('app.mailer_email');

        $subject = "Vous avez reçu un don pour votre projet";

        $content = "email_notifications/donation_received.html.twig";

        $contentParameters = [
          "presentation" => $concernedPresentation,
          "donationAmount" => number_format(($purchase->getAmount() /100), 2, ',', ' '),
          "donorMessage" => $purchase->getContentItem("donorMessage"),
        ];

        $mailerService->send($sender, "Propon", $receiverEmail, $subject, $content, $contentParameters);

        return new Response(200);
       


      // ... handle other event types
      default:
        echo 'Received unknown event type ' . $event->type;
    }


    return new Response(200);


  }



 

}

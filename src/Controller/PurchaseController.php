<?php

namespace App\Controller;

use App\Entity\Purchase;
use Stripe\PaymentIntent;
use App\Form\BuyerInfoType;
use App\Service\MailerService;
use App\Service\StripeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchaseController extends AbstractController
{
  
    /**
     * @Route("/purchase/ajax-payment-form/", name="ajax_purchase_payment_form")
     */
    public function ajaxPaymentForm(Request $request, StripeService $stripeService, EntityManagerInterface $manager, MailerService $mailer): Response
    {

        if ($request->isXmlHttpRequest()) {

            $proponPurchaseType = $request->request->get('proponPurchaseType');
            $userEmail = $request->request->get('userEmail');
            $totalAmount = $request->request->get('totalAmount');

            /** @var array $additionalInfo */
            $additionalInfo = $request->request->get('additionalInfo');
            
            $purchase = new Purchase();

            $purchase->setBuyerEmail($userEmail);
            $purchase->setAmount($totalAmount);
            $purchase->setContent($additionalInfo);
            $purchase->setType($proponPurchaseType);

            $paymentIntent = $stripeService->getPaymentIntent($purchase);

            $purchase->setToken($paymentIntent["id"]); //so that we can retrieve a purchase and update its status

            $manager->persist($purchase);
            $manager->flush();

            $sender = $this->getParameter('app.email.general_technical_sending');


            //notify propon administration
            $receiver = $this->getParameter('app.email.contact');

            $mailer->send($sender, 'Propon', $receiver, "To admin : payment intent.", 'Intention type: '.$proponPurchaseType.'. Additional info: '.json_encode($additionalInfo));


            $stripePaymentForm = $this->renderView(
                
                "utilities/stripe_payment.html.twig", 
                
                [
                    'clientSecret' => $paymentIntent->client_secret,
                    'stripePublicKey' => $stripeService->getPublicKey(),
                    'purchase' => $purchase,
                ]

            );

            return  new JsonResponse($stripePaymentForm);

        }

        return  new JsonResponse();

    }

  
    /**
    * @Route("/purchase/success/", name="purchase_payment_success")
    */
    public function paymentSuccess(): Response
    {

        return $this->render('purchase/success.html.twig', [
            
        ]);

    }




}

<?php

namespace App\Controller;

use App\Entity\Purchase;
use Stripe\PaymentIntent;
use App\Service\StripePayment;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchaseController extends AbstractController
{
  
    /**
     * @Route("/purchase/pay/", name="purchase_payment_form")
     */
    public function showCardform(Purchase $purchase, StripePayment $stripeService): Response
    {

        $paymentIntent = $stripeService->getPaymentIntent($purchase);

        return $this->render('purchase/payment.html.twig', [
            'clientSecret' => $paymentIntent->client_secret,
            'stripePublicKey' => $stripeService->getPublicKey(),
            'purchase' => $purchase,
        ]);

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

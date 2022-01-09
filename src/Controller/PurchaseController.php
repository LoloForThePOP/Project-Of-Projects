<?php

namespace App\Controller;

use Stripe\PaymentIntent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PurchaseController extends AbstractController
{
  
    /**
     * @Route("/purchase/pay/", name="purchase_payment_form")
     */
    public function showCardform(): Response
    {

        // Stripe test secret API key.
        \Stripe\Stripe::setApiKey('sk_test_51KEEIYCW4N9Dp51Q47GlgI7tvVN4wEiATXozyMoPXX31E7o9P2PbpXJeUw7cUoWR5hWsj29cyUOzdnPXlz3ymajt002wA4uGeJ');

                
        $paymentIntent = \Stripe\PaymentIntent::create([

            'amount' => 210,
    
            'currency' => 'eur',
    
            'automatic_payment_methods' => [
    
                'enabled' => true,
    
            ],
    
        ]);

        return $this->render('purchase/payment.html.twig', [
            'clientSecret' => $paymentIntent->client_secret,
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

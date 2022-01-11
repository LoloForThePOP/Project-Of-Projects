<?php

namespace App\Controller;

use App\Entity\Purchase;
use Stripe\PaymentIntent;
use App\Form\BuyerInfoType;
use App\Service\StripePayment;
use App\Form\ContactWebsiteType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchaseController extends AbstractController
{

    
    /**
    * @Route("/purchase/ask-question/", name="purchase_ask_question")
    */
    public function askQuestion(Request $request): Response
    {

        $buyerInfoForm = $this->createForm(
            BuyerInfoType::class, null,
            array(

                // Time protection
                'antispam_time'     => true,
                'antispam_time_min' => 4,
                'antispam_time_max' => 3600,
            )
        );

        $contactWebsiteForm = 
        
            $this->createForm(

                ContactWebsiteType::class, 
                null,
                array(

                    // Time protection
                    'antispam_time'     => true,
                    'antispam_time_min' => 4,
                    'antispam_time_max' => 3600,
                )
            );

        
        $buyerInfoForm->handleRequest($request);

        if ($buyerInfoForm->isSubmitted() && $buyerInfoForm->isValid()) {

            dd('ok');



        }

        
        $contactWebsiteForm->handleRequest($request);

        if ($contactWebsiteForm->isSubmitted() && $contactWebsiteForm->isValid()) {

            dd('ok cwf');



        }

        return $this->render('purchase/ask_question.html.twig', [
            'buyerInfoForm' => $buyerInfoForm->createView(),
            'contactWebsiteForm' => $contactWebsiteForm->createView(),
            'contactUsPhone' => $this->getParameter('app.contact_phone'),
        ]);

    }
  
  
    /**
     * @Route("/purchase/form/", name="purchase_payment_form")
     */
    public function showCardform(StripePayment $stripeService): Response
    {

        $purchase = new Purchase();
        $purchase->setBuyerEmail('joe@doe.com');
        $purchase->setContentItem('objectsToPay', 'lol');
        $purchase->setContentItem('total_amount', 2000);

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

<?php

namespace App\Controller;

use App\Entity\Purchase;
use Stripe\PaymentIntent;
use App\Form\BuyerInfoType;
use App\Service\StripePayment;
use App\Form\ContactWebsiteType;
use App\Service\MailerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchaseController extends AbstractController
{

    
    /**
    * @Route("/purchase/ask-question/{offer}", name="purchase_ask_question")
    */
    public function askQuestion($offer, Request $request, MailerService $mailer): Response
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

            $visitorEmail = $buyerInfoForm->get('email')->getData();
            $visitorPhone = $buyerInfoForm->get('phone')->getData();
            $sender = $this->getParameter('app.support_email');
            $receiver = $this->getParameter('app.support_email');

            $mailer->send($sender, 'Propon', $receiver, 'New visitor purchase inquiry', '<h4>Plan: '.$offer.'</h4><h4>Visitor Email: '.$visitorEmail.' - Phone: '.$visitorPhone.'</h4>');

            // flash message & redirect to login route
            $this->addFlash('success', 'Vos informations ont bien étées envoyées, nous vous recontacterons dans de brefs délais.');

            return $this->redirectToRoute('homepage');

        }
        
        $contactWebsiteForm->handleRequest($request);

        if ($contactWebsiteForm->isSubmitted() && $contactWebsiteForm->isValid()) {


            $visitorEmail = $contactWebsiteForm->get('authorEmail')->getData();
            $messageContent = $contactWebsiteForm->get('content')->getData();

            $sender = $this->getParameter('app.support_email');
            $receiver = $this->getParameter('app.support_email');

            $mailer->send($sender, 'Propon', $receiver, 'New visitor purchase inquiry', '<h4>Plan: '.$offer.'</h4><h4>Visitor Email: '.$visitorEmail.'</h4><h4>Message content:</h4> <p>'.$messageContent.'</p>');

            $this->addFlash('success', 'Votre message a bien été envoyé, nous vous recontacterons dans de brefs délais.');

            return $this->redirectToRoute('homepage');

        }

        return $this->render('purchase/ask_question.html.twig', [
            'buyerInfoForm' => $buyerInfoForm->createView(),
            'contactWebsiteForm' => $contactWebsiteForm->createView(),
            'contactUsPhone' => $this->getParameter('app.contact_phone'),
            'offer' => $offer,
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

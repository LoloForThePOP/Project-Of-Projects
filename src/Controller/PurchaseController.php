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

        $buyerInfoForm->handleRequest($request);

        if ($buyerInfoForm->isSubmitted() && $buyerInfoForm->isValid()) {

            //form content

            $visitorEmail = $buyerInfoForm->get('email')->getData();
            $visitorPhone = $buyerInfoForm->get('phone')->getData();
            $visitorMessage = $buyerInfoForm->get('message')->getData();


            //emailing
            $sender = $this->getParameter('app.general_contact_email');
            $receiver = $this->getParameter('app.general_contact_email');

            $mailer->send($sender, 'Propon', $receiver, 'New visitor purchase inquiry', '<h4>Plan: '.$offer.'</h4><h4>Visitor Email: '.$visitorEmail.' - Phone: '.$visitorPhone.'</h4><h4>Message content:</h4> <p>'.$visitorMessage.'</p>');

            // flash message & redirect to login route
            $this->addFlash('success', 'Votre message a bien été envoyé, nous vous recontacterons dans de brefs délais.');

            return $this->redirectToRoute('homepage');

        }


        return $this->render('purchase/ask_question.html.twig', [
            'buyerInfoForm' => $buyerInfoForm->createView(),
            'contactUsPhone' => $this->getParameter('app.contact_phone'),
            'offer' => $offer,
        ]);

    }
  
  
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

            $sender = $this->getParameter('app.general_contact_email');


            //notify propon administration

            $sender = $this->getParameter('app.general_contact_email');             
            $receiver = $sender;

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

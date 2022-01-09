<?php

namespace App\Service;

use App\Entity\Purchase;


class StripePayment {


    protected $publicKey;
    protected $secretKey;

    public function __construct() //getting appropriate public and secret key according to dev or prod environment
    {

        $this->publicKey = strtoupper(getenv('APP_ENV')).getenv('_STRIPE_PUBLIC_KEY');
        $this->secretKey = strtoupper(getenv('APP_ENV')).getenv('_STRIPE_SECRET_KEY');

    }

    public function getPublicKey(){
        return $this->publicKey;
    }


    public function getPaymentIntent(Purchase $purchase){

         // Stripe test secret API key.
         \Stripe\Stripe::setApiKey($this->secretKey);

                
         return \Stripe\PaymentIntent::create([
 
             'amount' => $purchase->getContent["amount"],
     
             'currency' => 'eur',
     
             'automatic_payment_methods' => [
     
                 'enabled' => true,
     
             ],
     
         ]);

    }


}

<?php

namespace App\Service;

use App\Entity\Purchase;


class StripeService {


    protected $publicKey;
    protected $secretKey;

    public function __construct() //getting appropriate stripe public and secret key according to dev or prod environment
    {

        $this->publicKey = $_ENV[strtoupper($_ENV['APP_ENV']).'_STRIPE_PUBLIC_KEY'];
        $this->secretKey = $_ENV[strtoupper($_ENV['APP_ENV']).'_STRIPE_SECRET_KEY'];

    }

    public function getPublicKey(){
        return $this->publicKey;
    }


    public function getPaymentIntent(Purchase $purchase){

         // Stripe test secret API key.
         \Stripe\Stripe::setApiKey($this->secretKey);

                
         return \Stripe\PaymentIntent::create([
 
             'amount' => $purchase->getAmount(),
     
             'currency' => 'eur',
     
             'automatic_payment_methods' => [
     
                'enabled' => true,
     
             ],
     
         ]);

    }


}

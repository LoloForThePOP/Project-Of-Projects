<?php

namespace App\Service;

use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PresentationValidator {

    protected $validator;

    public function __construct(ValidatorInterface $validator)
    {
        
        $this->validator = $validator;

    }

    
    /**
    * Check if a presentation item's content is valid.
    * Ex : name : website; property : url; content : www.propon.org
    */
    public function validate($property, $subproperty, $content){

        $constraints = null;

        switch ($property) {

            case 'websites':

                switch ($subproperty) {

                    case 'url':

                        $constraints = new Assert\Url(['message' => 'Vous devez utiliser une adresse web valide']);

                        break;
                    
                    default:
                        # code...
                        break;
                }
                break;
            
            default:
                # code...
                break;

        }

        // use the validator to validate the value
        $errors = $this->validator->validate(
            $content,
            $constraints
        ); 
            
        if ($errors->count() > 0) {

            return  $errors[0]->getMessage();
                    
        } else {

            return true;

        }



    }












}

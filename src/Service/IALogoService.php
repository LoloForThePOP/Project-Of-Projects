<?php

namespace App\Service;


class IALogoService {



    public function __construct()
    {
        
        

    }


    /**
    * return a prompt to create a logo according to user inputs.
    * user filled a step by step form.
    * For each step we concatenate prompt according to user input. 
    * To create appropriate string (step by step) we call the string createPromptChunk function.
    */

    public function createPrompt($dataArray){
                
       $prompt = "";

       foreach ($dataArray as $step => $value) {
        $prompt += $this->createPromptChunk($step, $value);
       }

        return $prompt;

    }

    /**
    * 
    */

    protected function createPromptChunk($step, $value){
                
       switch ($step) {

        case 'big_cat':
            
            return "I want a logo with $value";
            break;
        
        default:
            # code...
            break;
       }

    }


}

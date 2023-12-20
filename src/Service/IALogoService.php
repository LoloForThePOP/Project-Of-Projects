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

        dump($dataArray);

        foreach ($dataArray as $step => $value) {

            dump("treating step $step with value $value");

            if($value !== null || $value !== ""){

                $prompt = $prompt.$this->createPromptChunk($step, $value);

            }

            dump("prompt is $prompt");

        }

        return $prompt;

    }

    /**
    * 
    */

    protected function createPromptChunk($step, $value){

        $Fr_Prompt = "";
        
        switch ($step) {

            case 'big_cats':

                if ($value == "letters") {
                    $Fr_Prompt = "Créer un logo de type une ou plusieurs lettres. ";
                }

                elseif ($value == "image") {
                    $Fr_Prompt = "Créer un logo sans aucune lettre. ";
                }

                else {
                    $Fr_Prompt = "Créer un logo de type lettres et images. ";
                }
                
                break;

            case 'logo_type_letters':
                
                if ($value == "one-letter") {
                    $Fr_Prompt = "Ce logo devra contenir UNE SEULE lettre. ";
                }
                
                elseif ($value == "several-letters") {
                    $Fr_Prompt = "";
                }
                
                elseif ($value == "tangled-letters") {
                    $Fr_Prompt = "Les lettres devront être enlacées (par exemple comme avec le logo de la marque Yves Saint Laurent, ou le logo de la marque Louis Vuitton. ";
                }

                break;

            case 'logo_relative_positions':
                
                if ($value == "text-left") {
                    $Fr_Prompt = "Le texte est positionné à gauche de l'image. ";
                }
                
                elseif ($value == "text-right") {
                    $Fr_Prompt = "Le texte est positionné à droite de l'image. ";
                }
                
                elseif ($value == "text-bottom") {
                    $Fr_Prompt = "Le texte est positionné sous l'image. ";
                }
                
                elseif ($value == "text-top") {
                    $Fr_Prompt = "Le texte est positionné au dessus de l'image. ";
                }
                
                elseif ($value == "text-through") {
                    $Fr_Prompt = "Le texte est positionné à l'intérieur de l'image. ";
                }
                
                elseif ($value == "text-circle") {
                    $Fr_Prompt = "Le texte est positionné circulairement autour de l'image (text circle). ";
                }

                break;
            
            default:
                throw new \Exception("Unsupported step type");
                break;
       }

       return $Fr_Prompt;

    }


}

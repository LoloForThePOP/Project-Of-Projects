<?php

namespace App\Service;


class AILogoService {



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
                
        $prompt = "Une image sans bordure. Quatre suggestions de logo. Chaque logo est exactement centré dans chaque cadran qui occupe un quart de l'image et n'est pas découpé. Chaque logo est une forme simple reconnaissable, un logo moderne et minimaliste. Le fond de l'image doit être blanc. Pas d'effet de granularité. ";

        //dump($dataArray);

        foreach ($dataArray as $step => $value) {

            //dump("treating step $step with value $value");

            if($value !== null && $value !== ""){

                $prompt = $prompt.$this->createPromptChunk($step, $value);

            }

            //dump("prompt is $prompt");

        }

        return $prompt;

    }

    /**
    * Create a prompt chunk according to the step taken by the user and the value he provided
    */
    protected function createPromptChunk($step, $value){

        $Fr_prompt_chunk = "";
        
        switch ($step) {

            case 'big_cats':

                if ($value == "letters") {
                    $Fr_prompt_chunk = "Créer un logo de type une ou plusieurs lettres. ";
                }

                elseif ($value == "image") {
                    $Fr_prompt_chunk = "Créer un logo sans texte. ";
                }

                else {
                    $Fr_prompt_chunk = "Créer un logo de type lettres et images. ";
                }
                
                break;

            case 'logo_type_letters':
                
                if ($value == "one-letter") {
                    $Fr_prompt_chunk = "Ce logo devra contenir UNE SEULE lettre. ";
                }
                
                elseif ($value == "several-letters") {
                    $Fr_prompt_chunk = "Ce logo contiendra plusieurs lettres et SEULEMENT LES LETTRES SUIVANTES. Aucun autre tete ne doit être inséré !!!";
                }
                
                elseif ($value == "tangled-letters") {
                    $Fr_prompt_chunk = "Les lettres devront être enlacées (= emmêlées) (par exemple comme avec le logo de la marque Yves Saint Laurent, ou le logo de la marque Louis Vuitton. ";
                }

                break;

            case 'one_letter_user_choice':
                
                $Fr_prompt_chunk = "Cette lettre est la lettre $value. ";

                break;

            case 'several_letters_user_choice':
                
            $Fr_prompt_chunk = "Le texte à insérer dans le logo, en respectant l'ordre, les majuscules, et les éventuels espaces, est le suivant : $value.. ";

                break;

            case 'tangled_letters_user_choice':
                
                $Fr_prompt_chunk = "Les lettres à insérer et enlacer (= emmêler) entre elles dans le logo, en respectant l'ordre, les majuscules, et les éventuels espaces, sont les suivantes : $value. Elles DOIVENT être bien reconnaissables. ";

                break;

            case 'font_type':
                
                $Fr_prompt_chunk = "Voici la police de caractère à utiliser : $value. ";

                break;

            case 'letter_colors':
                
                $Fr_prompt_chunk = "Voici les instructions pour la couleur des lettres : $value. ";

                break;

            case 'logo_relative_positions':
                
                if ($value == "text-left") {
                    $Fr_prompt_chunk = "Le texte DOIT ETRE positionné à gauche de l'image. ";
                }
                
                elseif ($value == "text-right") {
                    $Fr_prompt_chunk = "Le texte DOIT ETRE positionné à droite de l'image. ";
                }
                
                elseif ($value == "text-bottom") {
                    $Fr_prompt_chunk = "Le texte DOIT ETRE positionné sous l'image. ";
                }
                
                elseif ($value == "text-top") {
                    $Fr_prompt_chunk = "Le texte DOIT ETRE positionné au dessus de l'image. ";
                }
                
                elseif ($value == "text-through") {
                    $Fr_prompt_chunk = "Le texte DOIT ETRE positionné à l'intérieur de l'image. ";
                }
                
                elseif ($value == "text-circle") {
                    $Fr_prompt_chunk = "Le texte DOIT ETRE positionné circulairement autour de l'image (text circle). ";
                }

                break;

            case 'image_text_relative_height':

                $relative_height = null;

                if (10 < $value && $value <= 15) {
                    $relative_height = "un quart de";
                }

                elseif (15 < $value && $value <= 25) {
                    $relative_height = "la moitiée de";
                }

                elseif (25 < $value && $value <= 35) {
                    $relative_height = "les trois quart de";
                }

                elseif (35 < $value && $value <= 45) {
                    $relative_height = "la même hauteur que";
                }

                elseif (45 < $value && $value <= 60) {
                    $relative_height = "une fois et demi";
                }
            
                $Fr_prompt_chunk = "Voici la hauteur relative de la totalité du texte par rapport à l'image : le texte du logo doit avoir une hauteur qui correspond à environ $relative_height la hauteur de l'image. ";

                break;
        
            case 'image_type':

                if ($value == "figurative-objects-description") {
                    $Fr_prompt_chunk = "L'image représente un objet de la réalité. ";
                }
                
                elseif ($value == "abstraction") {
                    $Fr_prompt_chunk = "L'image ne représente pas un objet de la réalité, c'est un objet abstrait. ";
                }
                
                else {
                    $Fr_prompt_chunk = "Le type d'image n'est pas connu. ";
                }

                break;
        
            case 'abstraction_object_description':
                
                $Fr_prompt_chunk = "Voici la description de l'objet abstrait à insérer dans le logo : $value. ";

                break;
    
        
            case 'figurative_object_description':
                
                $Fr_prompt_chunk = "Voici la description de l'objet de la réalité à insérer dans le logo : $value. ";

                break;

            case 'image_colors':
            
                $Fr_prompt_chunk = "Voici les instructions pour les couleurs de l'image : $value. ";

                break;    

            case 'user_last_requirements':
            
                $Fr_prompt_chunk = "Voici les dernières instructions pour la création du logo, elles ont une priorité haute par rapport aux instructions précédentes : $value. ";

                break;    
            
            default:
                throw new \Exception("Unsupported step type");
                break;
       }

       return $Fr_prompt_chunk;

    }


}

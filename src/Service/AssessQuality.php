<?php

namespace App\Service;

use App\Entity\PPBase;

class AssessQuality {


    /**
    * When user add a text description to its presentation, or a slide or a category or a keyword, then presentation quality is upgraded.
    */

    public function assessQuality (PPBase $presentation){

        $quality = 0;

        $textDescription = $presentation->getTextDescription();
        $keywords = $presentation->getKeywords();

        if ( 

            (isset($textDescription) && $textDescription !== '')
            
                ||

            count($presentation->getSlides()) > 0
            
            ) 

            {
                $quality++;
            }

        if (
            
            (isset($keywords) && $keywords !== '')
            
                || 

            count($presentation->getCategories()) > 0

            ) 
            
            {
                $quality++;
            }
                
        $presentation->setOverallQualityAssessment($quality);

    }

}

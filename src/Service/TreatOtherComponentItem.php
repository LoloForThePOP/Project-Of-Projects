<?php

namespace App\Service;

class TreatOtherComponentItem {

    /**
    * When a presentation structure is created or updated (ex: an image is added), we might do some routine task (ex: reduce image size). This class gather these tasks.
    */

    public function specificTreatments ($component_type, $elementToTreat){

        if ($elementToTreat == null) {

            return false;
        }

        switch ($component_type) {

            case 'websites':

                // see if we got a logo icon for this website

                $parse = parse_url($elementToTreat['url']);

                $availableWebsitesLogos = ["youtube.com", "linkedin.com", "facebook.com", "instagram.com", "twitch.tv", "twitter.com", "discord.com", "github.com", "tiktok.com"];

                $host = str_ireplace('www.', '', $parse['host']);                

                if(in_array($host, $availableWebsitesLogos)){

                    $domain = preg_replace('~\.(com|info|net|io|us|org|me|co\.uk|ca|mobi)\b~i','',$host);

                    $elementToTreat['icon'] = $domain;

                }

                else{

                    $elementToTreat['icon'] = null; 

                }
                
                return $elementToTreat;

            default:

                return $elementToTreat;
        }

    }




}

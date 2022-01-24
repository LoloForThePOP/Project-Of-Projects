<?php

namespace App\Service;

class TreatItem {

    /**
    * When a presentation structure is created or updated (ex: a website is added), we might do some routine task (ex: check if we have an icon for this website). This class gather these tasks.
    */
    public function specificTreatments ($component_type, $elementToTreat){

        if ($elementToTreat == null) {

            return false;
        }

        switch ($component_type) {

            case 'websites':

                // see if we got a logo icon for this website

                $parse = parse_url($elementToTreat['url']);

                $availableWebsitesLogos = ["youtube.com", "linkedin.com", "facebook.com", "instagram.com", "twitch.tv", "twitter.com", "discord.gg", "discord.com", "github.com", "tiktok.com", "trello.com", "pinterest.fr", "pinterest.com", "itch.io", "gamejolt.com"];

                $host = str_ireplace('www.', '', $parse['host']);                

                if(in_array($host, $availableWebsitesLogos)){

                    $domain = preg_replace('~\.(com|info|net|io|us|gg|org|me|co\.uk|ca|mobi)\b~i','', $host);

                    $elementToTreat['icon'] = $domain;

                }

                else{

                    $elementToTreat['icon'] = null; 

                }
                
                return $elementToTreat;

            case 'youtube_video': //used to extract youtube video identifier from user input. Sometimes, user gives a complete Youtube url (https://www...), but we just need video identifier. 

                $youtubeVideoIdentifier = $elementToTreat; //$elementToTreat is user input : a complete youtube url or directly the youtube video identifier.

                if (strpos($elementToTreat, 'youtu') !== false) {// just in case user provided a complete youtube video url instead of video identifier, in this case we extract the youtube video identifier
                    parse_str( parse_url( $elementToTreat, PHP_URL_QUERY ), $array_of_url_vars );
                    $youtubeVideoIdentifier = $array_of_url_vars['v'];
                }

                return $youtubeVideoIdentifier;

            default:

                return $elementToTreat;
        }

    }




}

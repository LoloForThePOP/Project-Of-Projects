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

                $availableWebsitesLogos = ["youtube.com", "linkedin.com", "facebook.com", "instagram.com", "twitch.tv", "twitter.com", "discord.gg", "discord.com", "github.com", "tiktok.com", "trello.com", "pinterest.fr", "pinterest.com", "itch.io", "gamejolt.com", "wikipedia.org"];

                $host = str_ireplace(['www.','fr.'], '', $parse['host']);                

                if(in_array($host, $availableWebsitesLogos)){

                    $domain = preg_replace('~\.(com|info|net|io|us|gg|org|me|co\.uk|ca|mobi)\b~i','', $host);

                    $elementToTreat['icon'] = $domain;

                }

                else{

                    $standardIcons = ['w1', 'w2', 'w3', 'w4', 'w5', 'w6', 'w7', 'w8'];

                    $elementToTreat['icon'] = $standardIcons[array_rand($standardIcons)]; 

                }
                
                return $elementToTreat;

            case 'youtube_video': //used to extract youtube video identifier from youtube url user input. 
                

                // Thanks at https://stackoverflow.com/questions/37186181/how-to-extract-youtube-id-from-this-url
                
                $url = urldecode(rawurldecode($elementToTreat));
                preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $url, $matches);
                $youtubeVideoIdentifier=$matches[1];

                return $youtubeVideoIdentifier;

            default:

                return $elementToTreat;
        }

    }




}

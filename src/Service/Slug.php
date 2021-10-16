<?php

namespace App\Service;

use App\Entity\PPBase;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
*
*/

class Slug {



    protected $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        
        $this->slugger = $slugger;
    }

    public function suggestSlug($entity){

        $suggestion = "";

        if ($entity instanceof PPBase) {

            if ($entity->getTitle() != null) {

                $suggestion = $entity->getTitle();

            }

            else {
                $suggestion = $entity->getGoal();
            }
        }

        return $this->slugInput($suggestion);

    }

    public function slugInput($string){

        $output = $string;

        $output = $this->slugger->slug($output);

        /* remove one letter words */
        $output = trim( preg_replace(
            "/[^a-z0-9']+([a-z0-9']{1,1}[^a-z0-9']+)*/i",
            " ",
            " $output "
        ) );

        /* replace non letter or digits by - */

        $output = preg_replace('~[^\pL\d]+~u', '-', $output);

        /* lower case the output */

        $output = strtolower($output);

        return $output;

    }






}

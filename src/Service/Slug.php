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

                $suggestion = $this->slugger->slug($entity->getTitle());

            }

            else {
                $suggestion = $this->slugger->slug($entity->getGoal());
            }
        }

        return strtolower($suggestion);

    }

    public function slugInput($string){

        return strtolower($this->slugger->slug($string));

    }






}

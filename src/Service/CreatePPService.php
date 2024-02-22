<?php

namespace App\Service;

use App\Entity\PPBase;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Stmt\Switch_;

class CreatePPService {

    protected $dataArray; // an array representing a project presentation (keys are goal, keywords, etc..)

    public function __construct($dataArray)
    {

        $this->dataArray = $dataArray;
        
    }


    /**
    * Save into db an actual Propon project presentation
    */

    public function createPP(){

        $pp = new PPBase();
                
        foreach ($this->dataArray as $key => $value) {

            switch ($key) {

                case 'goal':
                    $pp->setGoal($value);
                    break;
                
                default:
                    # code...
                    break;

            }

        }

        $em = new EntityManagerInterface();
        $em->persist($pp);
        $em->flush();


    }






}

<?php

namespace App\Service;

use App\Entity\CollectedData;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Propon collect some data in order to analyse product usage and try to improve it
 * Here is a simple method to do so
 */
class DataCollectService {

    //Accessing database from this service    
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {

        $this->entityManager = $entityManager;
        
    }

    /**
    * Save some data in DB
    *
    * Params: 
    *  - $dataType: (string) a short description of the type of data being collected (ex: "AI assistant")
    *  - $dataContent: (array) an array of actual collected data
    */

    public function save($dataType, ?array $dataContent, ){

        //Creating and hysrating a data collection object
        $dataObject = new CollectedData(); //The entity managing data
        $dataObject ->setDataType($dataType)
                    ->setData($dataContent);

        //Saving it to DB
        $this->entityManager->persist($dataObject);
        $this->entityManager->flush();

    }

}
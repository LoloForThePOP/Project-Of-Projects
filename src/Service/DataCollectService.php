<?php

namespace App\Service;

use App\Entity\CollectedData;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Context: we'd like to analyse Propon product usage in order to improve the product
 * We collect data to do so
 * Here is a simple service with one method to manage that
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
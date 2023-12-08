<?php

namespace App\Service;

use App\Entity\CollectedData;
use Doctrine\ORM\EntityManagerInterface;

class DataCollectservice {
    
    protected $dataType;
    protected ?array $dataContent;
    protected $entityManager;

    public function __construct($dataType, $dataContent, EntityManagerInterface $entityManager)
    {
        $this->dataType = $dataType;
        $this->dataContent = $dataContent;
        $this->entityManager = $entityManager;
        
    }

    /**
    * Save a data in db 
    */

    public function save(){

        $dataObject = new CollectedData();
        $dataObject ->setDataType($this->dataType)
                    ->setData($this->dataContent);

        $this->entityManager->persist($dataObject);
        $this->entityManager->flush();

    }

}
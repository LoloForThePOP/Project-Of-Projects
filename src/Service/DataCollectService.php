<?php

namespace App\Service;

use App\Entity\CollectedData;
use Doctrine\ORM\EntityManagerInterface;

class DataCollectService {
    
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {

        $this->entityManager = $entityManager;
        
    }

    /**
    * Save a data in db 
    */

    public function save($dataType, ?array $dataContent, ){

        $dataObject = new CollectedData();
        $dataObject ->setDataType($dataType)
                    ->setData($dataContent);

        $this->entityManager->persist($dataObject);
        $this->entityManager->flush();

    }

}
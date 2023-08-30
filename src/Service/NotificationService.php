<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;


class NotificationService {

    /*
    *
    * Categories of notifications
    *
    * Cat 0 : compulsory (highly important)
    * Cat 1 :
    */

    protected $em;
    protected $user;

    public function __construct(User $user, EntityManagerInterface $em)
    {
      
        $this->em = $em;
        $this->user = $user;

    }

    public function decide($notificationType){

    }

    public function prepare($notificationType, $parameters){

    }

    public function send(){// we send result from prepare

    }

    public function editPreference($notificationType, $preference){

    }




}

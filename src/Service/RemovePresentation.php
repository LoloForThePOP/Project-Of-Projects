<?php

namespace App\Service;

use App\Entity\PPBase;
use Doctrine\ORM\EntityManagerInterface;

class RemovePresentation {

    protected $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;

    }

    /**
    * When creator wants to delete her presentation, we clone minimal informations (e.g. goal + title) in order to inform potential "followers" that this presentation has been deleted (for exemple this presentation might have been bookmarked). 
    */

    public function removePresentation (PPBase $presentation){

        // keeping a footprint

        $presentationFootprint = new PPBase();

        $presentationFootprint
            ->setTitle($presentation->getTitle())
            ->setGoal($presentation->getGoal())
            ->setIsDeleted(true);

        $stringId = $presentation->getStringId();

        // Decoupling presentation with its conversations

        foreach ($presentation->getConversations() as $conversation) {

            $presentation->removeConversation($conversation);

        }

        $this->manager->flush();

        

        //removing project presentation with all its childs

        $this->manager->remove($presentation);

        //inserting cloned presentation

        $this->manager->persist($presentationFootprint);

        $this->manager->flush();

        //overiding automatic stringId 

        $presentationFootprint->setStringId($stringId);

        $this->manager->flush();

    }




}

<?php

namespace App\Service;

use App\Entity\Slide;
use App\Entity\PPBase;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CreatePPService {

    protected $em;
    protected $session;
    protected $security;
    protected $slugger;
    protected $pp;

    public function __construct(EntityManagerInterface $em, Security $security, SessionInterface $session, SluggerInterface $slugger)
    {

        $this->session = $session;
        $this->em = $em;
        $this->security = $security;
        $this->slugger = $slugger;
        
    }


    /**
    * Save into db an actual Propon project presentation
    * $dataArray : an array representing a project presentation (keys are goal, keywords, etc..)
    */
    public function create($dataArray){

        $this->pp = new PPBase();

        $this->ppUserCreatorManagement();
                
        foreach ($dataArray as $key => $value) {

            switch ($key) {

                case 'goal':
                    $this->pp->setGoal($value);
                    break;

                case 'description':
                    $this->pp->setTextDescription($value);
                    break;

                case 'keywords':
                    $this->pp->setKeywords($value);
                    break;

                case 'qas':

                    foreach ($value as $qa => $qaContent){
                        //dd($qaContent);                             
                        $this->pp->addOtherComponentItem("questionsAnswers", $qaContent);
                    }
                    
                    break;

                case 'isAdminValidated':

                    $this->pp->setIsAdminValidated($value);
                    
                    break;

                case 'imagePrompts':

                    // We generate presentation slides with caption but without an actual image file

                    foreach ($value as $imagesPrompts => $imagePrompt){

                        $imageSlide = new Slide();
                        $imageSlide
                            ->setType('image')
                            ->setCaption($imagePrompt)
                            ->setAddress("ai_generable")
                            ->setPresentation($this->pp);

                        $this->em->persist($imageSlide);

                    }
                    
                    break;
                
                default:
                    # code...
                    break;

            }

        }

        $this->em->persist($this->pp);
        $this->em->flush();

        return $this->pp->getStringId();

    }


    /**
    * PP needs a creator to be valid
    * If user is not logged in, we create a virtual/shadow user so that user can subscribe & connect after he sees the result
    */
    protected function ppUserCreatorManagement(){

        $user = $this->security->getUser();
        
        if ($user) {
            $this->pp->setCreator($user);
        }else{
            
            $newUserService = new UserService($this->em, $this->slugger, $this->session); 
            $newGuestUser = $newUserService->createSaveFakeUser();

            $this->pp->setCreator($newGuestUser)
                     ->setDataItem("guest-presenter-activated", false);

            $this->em->flush();

        }

        return true;

    } 
    





}

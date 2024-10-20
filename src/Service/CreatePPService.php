<?php

namespace App\Service;

use App\Entity\Slide;
use App\Entity\PPBase;
use App\Service\CreateUserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;


/**
 * Context: Given a structured array of information about a project (ex: "project goal" => "..."; "project title" => "..."; "project description" => "...") this class allows to create an actual Propon Project Presentation Page stored in DB.
 */
class CreatePPService {

    protected $pp; //project presentation we are creating
    protected $slugger; //project presentations have a friendly url adress, to do so we slug project goal or title if provided

    protected $security; //symgony security component allows to get current user object, we need it to hydrate project presentation creator
    
    protected $em; //entity manager to create and save project presentation in database

    protected $createUserService; //New project presentation needs a presentation creator. If user is an anonymous guest, CreateUserService stores in DB such a guest user so that project presention has a creator anyway.
    

    public function __construct(EntityManagerInterface $em, Security $security, SluggerInterface $slugger, CreateUserService $createUserService)
    {

        //see details above
        $this->em = $em;
        $this->security = $security;
        $this->slugger = $slugger;
        $this->createUserService = $createUserService;
        
    }


    /**
    * Save an actual Propon project presentation into db
    * Params: 
    *   
    *     - $dataArray : an array representing a project presentation (keys are {project goal; project description; etc.})
    */
    public function create($dataArray){

        $this->pp = new PPBase(); //instanciating a new 3P

        $this->ppCreatorManagement(); //This function allows to hydrate PP user depending on context (user logged in or not).
                
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
                        $this->pp->addOtherComponentItem("questionsAnswers", $qaContent);
                    }
                    
                    break;

                case 'isAdminValidated':

                    $this->pp->setIsAdminValidated($value);
                    
                    break;

                case 'privateMessagesActivation':

                    $this->pp->setParameter('arePrivateMessagesActivated', $value);
                    
                    break;

                case 'imagePrompts':

                    // We generate presentation slides with caption but without an actual image file

                    foreach ($value as $imagesPrompts => $imagePrompt){

                        $imageSlide = new Slide();
                        $imageSlide
                            ->setType('image')
                            ->setCaption($imagePrompt." (illustration imaginaire).")
                            ->setAddress("ai_generable")
                            ->setPresentation($this->pp);

                        $this->em->persist($imageSlide);

                    }
                    
                    break;
                
                default:
                    break;

            }

        }

        $this->em->persist($this->pp);
        $this->em->flush();

        return $this->pp->getStringId();

    }


    /**
    * A PP needs a creator to be valid. This function allows to hydrate PP creator attribute appropriately depending on current user context (user is logged in or user is a guest)
    * 
    */
    protected function ppCreatorManagement(){

        $user = $this->security->getUser(); //getting current user thanks to symfony security component
        
        if ($user) { //case user is logged in, we hydrate project presentation creator attribute with logged in user
            $this->pp->setCreator($user);
        }else{ //current user is not logged in, current is a guest user, so PP creator is a guest user we got to create(see CreateUserService for details about creating a guest user in DB)
            
            $newGuestUser = $this->createUserService->createSaveGuestUser();

            $this->pp->setCreator($newGuestUser)->setDataItem("guest-presenter-activated", false);

            $this->em->flush();

        }

        return true;

    } 
    





}

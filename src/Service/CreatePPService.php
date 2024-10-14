<?php

namespace App\Service;

use App\Entity\Slide;
use App\Entity\PPBase;
use App\Service\CreateUserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


/**
 * Context: Given a structured array of information about a project (ex: "project goal" => "..."; "project title" => "..."; "project description" => "...") this class allows to create an actual Propon Project Presentation Page stored in DB.
 */
class CreatePPService {

    protected $pp; //project presentation we are creating
    protected $slugger; //project presentations have a friendly url adress, to do so we slug project goal or title if provided

    protected $security; //symgony security component allows to get current user object, we need it to hydrate project presentation creator
    protected $session; //allows to access in session some information about user
    
    protected $em; //entity manager to create and save project presentation in database
    

    public function __construct(EntityManagerInterface $em, Security $security, SessionInterface $session, SluggerInterface $slugger)
    {

        //see details above
        $this->session = $session;
        $this->em = $em;
        $this->security = $security;
        $this->slugger = $slugger;
        
    }


    /**
    * Save an actual Propon project presentation into db
    * Params: 
    *   
    *     - $dataArray : an array representing a project presentation (keys are {project goal; project description; etc.})
    */
    public function create($dataArray){

        $this->pp = new PPBase(); //instanciating a new 3P

        $this->ppUserCreatorManagement(); //This function allows to hydrate PP user depending on context (user logged in or not).
                
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
                    # code...
                    break;

            }

        }

        $this->em->persist($this->pp);
        $this->em->flush();

        return $this->pp->getStringId();

    }


    /**
    * PP needs a creator to be valid. This function allows to hydrate PP user appropriately depending on user context (user is logged in or not)
    * 
    */
    protected function ppUserCreatorManagement(CreateUserService $createUserService){

        $user = $this->security->getUser(); //getting current user thanks to symfony security component
        
        if ($user) { //case user is logged in, it's easy we hydrate project presentation user with it
            $this->pp->setCreator($user);
        }else{ //current user is not logged in
            
            $newGuestUser = $createUserService->createSaveGuestUser();

            $this->pp->setCreator($newGuestUser)->setDataItem("guest-presenter-activated", false);

            $this->em->flush();

        }

        return true;

    } 
    





}

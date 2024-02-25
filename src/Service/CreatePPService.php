<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Slide;
use App\Entity\PPBase;
use App\Service\AI\AICreateImageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class CreatePPService {

    protected $em;
    protected $security;
    protected $pp;

    public function __construct(EntityManagerInterface $em, Security $security)
    {

        $this->em = $em;
        $this->security = $security;
        
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

                        //$this->pp->addSlide($imageSlide);

                        
                        //dd($imgURL);
                        //dd($qaContent);                             
                        //$this->pp->addOtherComponentItem("questionsAnswers", $qaContent);
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
    * If user is not logged in, we create a virtual/shadow/temp (!) user so that user can subscribe & connect after he sees the result
    */
    protected function ppUserCreatorManagement(){

        $user = $this->security->getUser();
        
        if ($user) {
            $this->pp->setCreator($user);
        }else{
            
            $newUser= new User(); 
            $anonymousUserNameId = substr(str_shuffle(MD5(microtime())), 0, 6); // creating a random username for temp user
            $newUser->setUserName('temp-'.$anonymousUserNameId)
                    ->setEmail('temp-'.$anonymousUserNameId.'@test.com')
                    ->setUserNameSlug(strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $newUser->getUserName()))))
                    ->setPassword('test'.$anonymousUserNameId)
                    ->setParameter('isVerified', true);

            $this->pp->setCreator($newUser)
                    ->setDataItem("guest-presenter-activated", false);

            $this->em->persist($newUser);
            $this->em->flush();
        }

        return true;

    } 
    





}

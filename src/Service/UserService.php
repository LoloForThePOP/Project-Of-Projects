<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Persorg;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class UserService {

    protected $user;
    protected $em;
    protected $slugger;
    protected $session;

    public function __construct(EntityManagerInterface $em, SluggerInterface $slugger, SessionInterface $session, User $user = null)
    {

        $this->user = $user;
        $this->em = $em;
        $this->slugger = $slugger;
        $this->session = $session;
        
    }


    /**
    * Solid user means user is not a shadow guest user
    * arg $createPassword : we create a dumb password when user is create via facebook or google auth
    */
    public function saveSolidUser($createPassword = false){

        if ($this->user->getEmail() == null) {
           throw new Exception("Creating a solid user means knowing its email adress");
        }

        if ($this->user->getUserName() == null) {
            $this->user->setUserName("std-sld-".substr(md5(rand()), 0, 8));
        }

        $this->user->setUserNameSlug($this->slugger->slug($this->user->getUserName()));

        if ($createPassword == true) {
            $this->user->setPassword(substr(md5(rand()), 0, 8));
        }

        //creating a persorg for user

        $newUserPersorg = new Persorg(); // creating a user profile
        $newUserPersorg->setName($this->user->getUserName());
        $this->user->setPersorg($newUserPersorg);

        //saving in db

        $this->em->persist($newUserPersorg);
        $this->em->persist($this->user);
        $this->em->flush();

        return $this->user;

    }

    

    /**
    * Sometime we need to create a fake user in order to let unregistered user use some products (ex: create a 3p and play with it)
    */
    public function createSaveFakeUser(){

        $this->user = new User();

        $randomString = substr(md5(rand()), 0, 8);

        $this->user->setUserName("std-gst-".$randomString)
                    ->setEmail('ppn-temp-'.$randomString.'@test.com')
                    ->setUserNameSlug($this->slugger->slug($this->user->getUserName()))
                    ->setPassword(substr(md5(rand()), 0, 8))
                    ->setDataItem("guestUser", true)
                    ->setParameter('isVerified', false);

        $this->em->persist($this->user);
        $this->em->flush();

        $sessionVariablesService = new SessionVariablesService($this->session);

        $sessionVariablesService->fakeUserId($this->user); //storing fake user id in a session so that we can transfert its content to an actual logged in user.

        return $this->user;
       
    }




}

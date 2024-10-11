<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Persorg;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


/**
 * This class allows to save users in database.
 * 
 * Two cases are treated in this class (one method for each case):
 * 
 *  - case 1: user just have subscribed to the app (including Google auth and Facebook auth): in this case we know its email adress, we save this user in DB. (called a solid user)
 * 
 *  - case 2: user is a guest: he has not logged in but he has created a presentation: in this case we create and save a guest user with a fake email address & with access to the just created project presentation.
 * 
 */
class UserService {

    protected $user; //user object that will be hydrated and saved in DB (if no one is provided (user object) we instantiate one)
    protected $em; //symfony entity manager to save user entity in DB
    protected $slugger; //user accounts have a friendly url adress, to do so we slug user name.
    protected $session; //accessing session variables allows us to know if user is a guest or not

    public function __construct(EntityManagerInterface $em, SluggerInterface $slugger, SessionInterface $session, User $user = null)
    {

        $this->user = $user;
        $this->em = $em; 
        $this->slugger = $slugger;
        $this->session = $session;
        
    }


    /**
    * 
    * Allows saving a user whose email address is known
    * 
    * Arguments: $createPassword: (boolean) if set to true we create a password (ex: user is created via a facebook or google auth)
    *
    * Return the user object.
    *
    * Note: Solid user means user is not a "virtual" guest user.
    */
    public function saveSolidUser($createPassword = false){

        if ($this->user->getEmail() == null) {//this function only handles cases where user email is known
           throw new Exception("Creating a solid user means knowing its email adress");
        }

        if ($this->user->getUserName() == null) {//if user name is unknown we create a fake one
            $this->user->setUserName("std-sld-".substr(md5(rand()), 0, 8));// std means standard (we don't know username so we call it standard); sld means solid user (= not a guest user)
        }

        $this->user->setUserNameSlug($this->slugger->slug($this->user->getUserName()));//creating a username slug so that user has a friendly account url (think of x.com account urls)

        if ($createPassword == true) {//if no password is provided we create a random one
            $this->user->setPassword(substr(md5(rand()), 0, 8));
        }

        //Creating a minimal user profile (user profile or organisation profile is called a persorg)

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
     * This function allows to create a guest user and save it to DB.
     * 
     * Use case: we want to let unregistered users or not logged in users use some app products (ex: create a 3p and play with it). Creating a guest user in db facilitates the retrieval of user creations in case he/she wants to subscribe to the app.
     * 
     * Return the user object
    */
    public function createSaveGuestUser(){

        $this->user = new User();// instantiating a new user object

        $randomString = substr(md5(rand()), 0, 8);//this random string is used to create some guest user attributes like he/her name

        $this->user->setUserName("std-gst-".$randomString)// std means standard (we don't know username so we call it standard); gst means guest user (= not a solid user)
                    ->setEmail('ppn-temp-'.$randomString.'@test.com')//guest user fake email adress
                    ->setUserNameSlug($this->slugger->slug($this->user->getUserName()))//creating a slug (required attribute)
                    ->setPassword(substr(md5(rand()), 0, 8)) //fake password
                    ->setDataItem("guestUser", true) //declaring that user is a guest
                    ->setParameter('isVerified', false); //guest user has minimal rights as long as he's guest

        //saving guest user in db

        $this->em->persist($this->user);
        $this->em->flush();

        //managing app session variables
        $sessionVariablesService = new SessionVariablesService($this->session);
        //storing our current guest user id in a session variable so that we can match the online anonymous user with the db stored guest user (in other words anonymous user is tracked with db guest user id stored in session). 
        $sessionVariablesService->guestUserId($this->user); 

        return $this->user;
       
    }




}

<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Persorg;
use App\Service\MailerService;
use App\Service\SessionVariablesService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;


/**
 * This class allows to save newly created users in database.
 * 
 * Two users types are treated in this class (one method for each case):
 * 
 *  - type 1: user just have subscribed to the app (including app register form; Google auth or Facebook auth): in this case we know user's email adress, we save this user in DB, potentially with an email verification process (such users are called authenticated users).
 * 
 *  - type 2: user is a guest: he has not logged in but he has created a presentation: in this case we create and save a guest user with a fake email address & with access to the just created project presentation.
 * 
 * Another method allows to transfert a anonymous guest user work to a authenticated created user (= when user authenticates to the app after creating a project presentation as an anonymous guest user, we transfert the guest user work to the newly authenticated user so that they retrieve their work).
 * 
 */
class CreateUserService {

    protected $em; //symfony entity manager to save user entity in DB
    protected $slugger; //user accounts have a friendly url adress, to do so we slug user name.
    protected $sessionVariablesService; //accessing session variables allows us to know if user is a guest or not
    protected $passwordHasher; //to hash plain password provided by user
    protected $tokenGenerator; //user email validation: the generated token is used to create a unique verification url that user has to click on.
    protected $urlGenerator; //user email validation: to actually create the above mentioned unique url 
    protected $mailerService; //user email validation: to actually send the email with above mentioned unique url to click on.

    public function __construct(EntityManagerInterface $em, SluggerInterface $slugger, SessionVariablesService $sessionVariablesService,UserPasswordHasherInterface $passwordHasher, TokenGeneratorInterface $tokenGenerator, UrlGeneratorInterface $urlGenerator, MailerService $mailerService)
    {

        $this->em = $em; 
        $this->slugger = $slugger;
        $this->sessionVariablesService = $sessionVariablesService;
        $this->passwordHasher = $passwordHasher;
        $this->tokenGenerator = $tokenGenerator;
        $this->urlGenerator = $urlGenerator;
        $this->mailerService = $mailerService;
        
    }


    /**
    * 
    * Allows to save a user whose email address is known
    * 
    * Arguments:
    *
    *   - (object) $user: the user we'd like to format $ save in db.
    *   - (boolean) $createUsername: if set to true we create a random user name.
    *  
    *   - $plainPassword (default = null): if null we create a password (ex: user is created via a facebook or google auth)
    *   - (boolean) $checkUserEmail: if set to true we create and send an email with a unique url that user has to click on in order to validate his email address.
    *
    * Return the user object.
    *
    */
    public function saveAuthenticatedUser(User $user = null, $createUsername = false, $plainPassword = null, $checkUserEmail = false){

        if ($user->getEmail() == null) {//this function only handles cases where user email is known
           throw new Exception("Creating an authenticated user means knowing its email adress");
        }

        if ($createUsername == true) {//if user name is unknown we create a fake one
            $user->setUserName("std-auth-".substr(md5(rand()), 0, 7));// std means standard (we don't know username so we call it standard); auth means authenticated user (= not a guest user)
        }

        /* userName unique slug creation*/

        //creating a username slug so that user has a friendly account url (ex: propon.org/user/jack-5)
        $userNameSlugCandidate = $this->slugger->slug($user->getUserName());
        
        //checking if this username slug candidate is unique in DB
        $slugs = $this->em->getRepository(User::class)->createQueryBuilder('u')->where('u.userNameSlug LIKE :slug')->setParameter('slug', $userNameSlugCandidate.'%')->getQuery()->getResult();

        if (!empty($slugs)) {//If username slug is not unique we try to create a unique similar one

            $userNameSlugCandidate .= '-' . count($slugs); //this method does not work if user rows can be deleted + it does not provide reliable increment (ex: "My post" will result as my-post but then "My" will result as my-1 (instead of just my))

        }

        $user->setUserNameSlug($userNameSlugCandidate);

        // user password management
        if ($plainPassword == null) {//if no password is provided we create a random one
            $plainPassword = substr(md5(rand()), 0, 7);
        } 

        
        //dd($plainPassword);

        // hashing user password
        $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));

        // creating a minimal user public profile (user profile or organisation profile is called a persorg (contraction of person or organisation))

        $userPersorg = new Persorg();
        $userPersorg->setName($user->getUserName());
        $user->setPersorg($userPersorg);

        // generating a token for user email validation if appropriate

        if ($checkUserEmail == true) {

            $token = $this->tokenGenerator->generateToken();
            $user->setEmailValidationToken($token);

            //Generating url for user email confirmation
            $confirmationURL = $this->urlGenerator->generate('email_confirmation', array('token' => $token), $this->urlGenerator::ABSOLUTE_URL);

            //Sending email to user for email confirmation

            $this->mailerService->send('mailer@propon.org', 'Propon', $user->getEmail(), "Propon - Confirmez votre adresse e-mail", 'registration/email_confirm_email.html.twig', ['confirmationURL' => $confirmationURL]);

        }

        // saving created user and its profile in db

        $this->em->persist($userPersorg);
        $this->em->persist($user);
        $this->em->flush();

        return $user;

    }

    

    /**
     * This function allows to create a guest user and save it to DB.
     * 
     * Use case: we want to let unregistered users or not logged in users use some app products (ex: create a 3p and play with it). Creating a guest user in db facilitates the retrieval of user creations in case he/she wants to subscribe to the app.
     * 
     * Return the user object
    */
    public function createSaveGuestUser(){

        $user = new User();// instantiating a new user object

        $randomString = substr(md5(rand()), 0, 8);//this random string is used to create some guest user attributes like he/her name

        $user->setUserName("std-gst-".$randomString)// std means standard (we don't know username so we call it standard); gst means guest user (= not an authenticated user)
                    ->setEmail('ppn-temp-'.$randomString.'@test.com')//guest user fake email adress
                    ->setUserNameSlug($this->slugger->slug($user->getUserName()))//creating a slug (required attribute)
                    ->setPassword(substr(md5(rand()), 0, 8)) //fake password
                    ->setDataItem("guestUser", true) //declaring that user is a guest
                    ->setParameter('isVerified', false); //guest user has minimal rights as long as he's guest

        //saving guest user in db

        $this->em->persist($user);
        $this->em->flush();

        //storing our current guest user id in a session variable so that we can match the online anonymous user with the db stored guest user (in other words anonymous user that has done some work with the app is tracked with db guest user id stored in session). 
        $this->sessionVariablesService->guestUserId($user); 

        return $user;
       
    }


    /**
     * 
     * 
     * When user authenticates to the app after creating a project presentation as an anonymous guest user, this function allows to transfert the guest user work saved in DB to the newly authenticated user so that they retrieve their work.
     * 
     * Return false if no work has to be transfered (ex: user authenticates without having previously created a presentation as a guest). Return project presentation stringId otherwise so we can redirect newly authenticad user to the presentation he has created as a guest.
     * 
    */
    public function transfertGuestUserWork(User $user){

        //checking if authenticating user has just been a guest user anonymously presenting a project (true if not null).
        $guestUserId = $this->sessionVariablesService->guestUserId(); 

        if ($guestUserId !== null) {//if authenticating user have a guest user id stored in a session variable, it means that they just used the app as a guest and now he authenticate to the app.

            $guestUser = $this->em->getRepository(User::class)->findOneBy(['id' => $guestUserId]);//searching in db the guest user matching with the online anonymous user that is logging into the app.

            $guestUserPresentation = $guestUser->getCreatedPresentations()[0];//Searching the db stored project presentation done by the guest user.

            $guestUserPresentation->setDataItem("guest-presenter-activated", true);//Flagging that this presentation comes from a previously guest anonymous user

            $user->addCreatedPresentation($guestUserPresentation);//Adding the presentation to the newly authenticating user

            $guestUser->removeCreatedPresentation($guestUserPresentation);//Removing the project presentation from the guest user stored in db (we don't need this user anymore, he served as a storage)
        
            $this->em->flush();

            $this->sessionVariablesService->guestUserId(null, true); //Delete the appropriate session variable, we don't use it anymore since user has authenticated.

            return $guestUserPresentation->getStringId(); //To redirect authenticated user to the presentation page they have created as a guest.
            
        }

        return false;

    }







}

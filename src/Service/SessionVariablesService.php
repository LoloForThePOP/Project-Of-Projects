<?php

namespace App\Service;


use App\Entity\User;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * This class centralizes management of some session variables used in the app
 */
class SessionVariablesService {

    protected $session; //session object to handle session variables

    public function __construct(SessionInterface $session)
    {

        $this->session = $session;
        
    }


    /**
     * Context: anonymous user can use the app as a demo without being logged in but how to give him back his / her work if this anonymous user wants to register or log into the app ? A session variable is a solution: it keeps a link between online anonymous user and db stored works he's done as a guest. So now if anonymous user subsribe / log into the app, we can give him back the work he has done as an anonymous user thanks to the session variable.
     *
     * Params: 
     * 
     *   - $guestUser: (User) (nullable) a db stored guest user
     *   - $delete: (boolean) delete the session variable if set to true (default is false)
     * 
     * if a User object is given as an argument we SET the session variable with this user id.
     * if no User object is given as an argument and we have an existing session variable, we GET its value.
     * 
     */
    public function guestUserId(User $guestUser = null, bool $delete = false){

        if ($guestUser !== null) {//If a guest user work is stored in db, we SET its id in a session variable so that we can match his / her work with the online anonymous user that have actually done the work. 
            $this->session->set("guest-user-id", $guestUser->getId());
            
            return;
        }
        
        //If no User object is given as an argument in the function + the session variable exists, we GET its value
        if ($this->session->has("guest-user-id")) {

            return $this->session->get("guest-user-id");
            
        }

        if ($delete == true) {
            
            $this->session->remove('guest-user-id');

        }
        
        return null; //case no User object is given as function argument + no sesion variable declared.

    }



}

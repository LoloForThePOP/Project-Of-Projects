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
     * 
     * 
     */
    public function guestUserId(User $fakeUser = null){

        if ($fakeUser !== null) {//If user is fake and have an id, we store it in session
            $this->session->set("fake-user-id", $fakeUser->getId());
            
            return;
        }

        if ($this->session->has("fake-user-id")) {//If a fake-user-id is stored in session we get it

            return $this->session->get("fake-user-id");
            
        }
        
        return null; 

    }



}

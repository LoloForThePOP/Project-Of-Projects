<?php

namespace App\Service;


use App\Entity\User;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Allows to centralize and manage Propon session variables
 */

class SessionVariablesService {

    protected $session;

    public function __construct(SessionInterface $session)
    {

        $this->session = $session;
        
    }

    public function fakeUserId(User $fakeUser = null){

        if ($fakeUser !== null) {
            $this->session->set("fake-user-id", $fakeUser->getId());
            
            return;
        }

        if ($this->session->has("fake-user-id")) {

            return $this->session->get("fake-user-id");
            
        }
        
        return null;

    }


}

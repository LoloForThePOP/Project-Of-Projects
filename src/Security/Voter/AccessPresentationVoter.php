<?php

namespace App\Security\Voter;

use App\Entity\PPBase;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class AccessPresentationVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['view', 'edit'])
            && $subject instanceof PPBase;
    }

    protected function voteOnAttribute(string $attribute, $presentation, TokenInterface $token): bool
    {
        $user = $token->getUser();

        switch ($attribute) {

            case 'view':

                return $this->canView($presentation, $token);

                break;

            case 'edit':

                return $this->canEdit($presentation, $token);

                break;
        }

        return false;
    }


    private function canEdit(PPBase $presentation, TokenInterface $token)
    {

        // !! $token->getUser() represents an user ONLY if user is logged in. Otherwise, it is not an instance of class User. To check if user is not logged in (i.e. anonymous), test !$token->getUser() instanceof UserInterface

        $user = $token->getUser();

        // if presentation has been deleted, it can not be edited
        if ($presentation->getIsDeleted()) {
            return false;
        }

        // if user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // otherwise we check if user is presentation's creator
        if ($user == $presentation->getCreator()) {
            return true;
        }

        return false;
    }

    private function canView(PPBase $presentation, TokenInterface $token)
    {

        $user = $token->getUser();

        // if user can edit, he can view
        if ($this->canEdit($presentation, $token)) {
            return true;
        }

        // if presentation is not published, other users can not view it
        if (!$presentation->getIsPublished()) {
            return false;
        }

        // if presentation has been deleted, other users can not view it
        if ($presentation->getIsDeleted()) {
            return false;
        }

        return true;
    }
}

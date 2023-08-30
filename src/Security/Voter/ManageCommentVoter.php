<?php

namespace App\Security\Voter;

use App\Entity\Comment;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ManageCommentVoter extends Voter
{

    private $requestStack;

    public function __construct(RequestStack $requestStack) {

        $this->requestStack = $requestStack;

    }


    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['delete', 'update'])
            && $subject instanceof Comment;
    }

    protected function voteOnAttribute(string $attribute, $comment, TokenInterface $token): bool
    {

        $user = $token->getUser();


        switch ($attribute) {

            case 'update':

                return $this->canUpdate($comment, $token);

                break;

            case 'delete':

                return $this->canDelete($comment, $token);

                break;
        }

        return false;
    }

    private function canUpdate(Comment $comment, TokenInterface $token)
    {

        // !! $token->getUser() represents an user ONLY if user is logged in. Otherwise, it is not an instance of class User. To check if user is not logged in (i.e. anonymous), test !$token->getUser() instanceof UserInterface

        $user = $token->getUser();

        // if user is an admin
        if(in_array('ROLE_ADMIN', $user->getRoles())){
            return true;
        }

        // if user is comment creator
        if ($user == $comment->getUser()){
            return true;
        }

        return false;
    }

    private function canDelete(Comment $comment, TokenInterface $token)
    {

        // !! $token->getUser() represents an user ONLY if user is logged in. Otherwise, it is not an instance of class User. To check if user is not logged in (i.e. anonymous), test !$token->getUser() instanceof UserInterface

        $user = $token->getUser();

        // if user is an admin
        if(in_array('ROLE_ADMIN', $user->getRoles())){
            return true;
        }

        // if user is comment creator
        if ($user == $comment->getUser()){
            return true;
        }

        return false;
    }

}

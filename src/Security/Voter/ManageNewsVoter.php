<?php

namespace App\Security\Voter;

use App\Entity\News;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ManageNewsVoter extends Voter
{

    private $requestStack;

    public function __construct(RequestStack $requestStack) {

        $this->requestStack = $requestStack;

    }


    protected function supports(string $attribute, $subject): bool
    {

        return in_array($attribute, ['delete', 'edit'])
            && $subject instanceof News;
    }

    protected function voteOnAttribute(string $attribute, $news, TokenInterface $token): bool
    {

        $user = $token->getUser();

        switch ($attribute) {

            case 'edit':

                return $this->canEdit($news, $token);

                break;


            case 'delete':

                return $this->canDelete($news, $token);

                break;
        }

        return false;
    }

    private function canEdit(News $news, TokenInterface $token)
    {

        // !! $token->getUser() represents an user ONLY if user is logged in. Otherwise, it is not an instance of class User. To check if user is not logged in (i.e. anonymous), test !$token->getUser() instanceof UserInterface

        $user = $token->getUser();

        // if user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // if user is an admin, he can edit
        if(in_array('ROLE_ADMIN', $user->getRoles()) || in_array('ROLE_NEWS_MANAGE', $user->getRoles())){
            return true;
        }

        // if user is article creator
        if ($user == $news->getAuthor()){
            return true;
        }

        return false;
    }


    private function canDelete(News $news, TokenInterface $token)
    {

        // !! $token->getUser() represents an user ONLY if user is logged in. Otherwise, it is not an instance of class User. To check if user is not logged in (i.e. anonymous), test !$token->getUser() instanceof UserInterface

        $user = $token->getUser();

        // if user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // if user is an admin
        if(in_array('ROLE_ADMIN', $user->getRoles()) || in_array('ROLE_NEWS_MANAGE', $user->getRoles())){
            return true;
        }

        // if user is news creator
        if ($user == $news->getAuthor()){
            return true;
        }

        return false;
    }








}

<?php

namespace App\Security\Voter;

use App\Entity\Article;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ManageArticleVoter extends Voter
{

    private $requestStack;

    public function __construct(RequestStack $requestStack) {

        $this->requestStack = $requestStack;

    }


    protected function supports(string $attribute, $subject): bool
    {
        // admin edit means can (un)validate article, edit thumbnail, edit slug
        return in_array($attribute, ['delete', 'user_edit', 'admin_edit'])
            && $subject instanceof Article;
    }

    protected function voteOnAttribute(string $attribute, $article, TokenInterface $token): bool
    {

        $user = $token->getUser();

        switch ($attribute) {

            case 'user_edit':

                return $this->canUserEdit($article, $token);

                break;

            case 'admin_edit':

                return $this->canAdminEdit($article, $token);

                break;

            case 'delete':

                return $this->canDelete($article, $token);

                break;
        }

        return false;
    }

    private function canUserEdit(Article $article, TokenInterface $token)
    {

        // !! $token->getUser() represents an user ONLY if user is logged in. Otherwise, it is not an instance of class User. To check if user is not logged in (i.e. anonymous), test !$token->getUser() instanceof UserInterface

        $user = $token->getUser();

        // if user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // if user is an admin, he can edit
        if(in_array('ROLE_ADMIN', $user->getRoles()) or in_array('ROLE_ARTICLE_EDIT', $user->getRoles())){
            return true;
        }

        // if user is article creator
        if ($user == $article->getAuthor()){
            return true;
        }

        return false;
    }

    private function canAdminEdit(Article $article, TokenInterface $token)
    {

        // !! $token->getUser() represents an user ONLY if user is logged in. Otherwise, it is not an instance of class User. To check if user is not logged in (i.e. anonymous), test !$token->getUser() instanceof UserInterface

        $user = $token->getUser();

        // if user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // if user is an admin, he can edit
        if(in_array('ROLE_ADMIN', $user->getRoles()) || in_array('ROLE_ARTICLE_EDIT', $user->getRoles())){
            return true;
        }

        return false;
    }

    private function canDelete(Article $article, TokenInterface $token)
    {

        // !! $token->getUser() represents an user ONLY if user is logged in. Otherwise, it is not an instance of class User. To check if user is not logged in (i.e. anonymous), test !$token->getUser() instanceof UserInterface

        $user = $token->getUser();

        // if user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // if user is an admin
        if(in_array('ROLE_ADMIN', $user->getRoles())){
            return true;
        }

        // if user is article creator
        if ($user == $article->getAuthor()){
            return true;
        }

        return false;
    }








}

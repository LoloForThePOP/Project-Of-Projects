<?php

namespace App\Service;

use Assert\Email;
use Assert\Length;
use Assert\NotBlank;
use App\Entity\Comment;
use Assert\GreaterThan;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class CommentService {

    protected $em;
    protected $validator;
    protected $security;


    public function __construct(Security $security, EntityManagerInterface $em, ValidatorInterface $validator)
    {
      
        $this->security = $security;
        $this->em = $em;
        $this->validator = $validator;

    }


    public function validateComment(Comment $comment){

        $errors = null;

        $constraints = [

            new Assert\NotBlank(['message' => 'Veuillez remplir ce champ']),
            new Assert\Length([
                'min' => 1,
                'max' => 2000,
                'minMessage' => 'Votre message doit faire minimum {{ limit }} caractère',
                'maxMessage' => 'Votre message doit faire maximum {{ limit }} caractères',
            ]),
        ];

        $trimmedcommentContent = trim($comment->getContent());

        
        // use the validator to validate the value
        $errors = $this->validator->validate(
            $trimmedcommentContent,
            $constraints
        ); 

       
        if ($errors->count() > 0) {

            return  $errors[0]->getMessage();
                    
        }

        //check if user doesn't comment too much in a short timeframe (spamming)

        $user = $comment->getUser();

        $userLastCommentTimespan = time() - $user->getComments()->first()->getCreatedAt()->getTimestamp();


        $constraints = [

            new Assert\GreaterThan(['value'=> 20, 'message' => 'Veuillez patienter quelques secondes avant d\'ajouter un nouveau commentaire !']),
            
        ];

    
        // use the validator to validate the value
        $errors = $this->validator->validate(
            $userLastCommentTimespan,
            $constraints
        );
    
        if ($errors->count()) {
            return  $errors[0]->getMessage();
        } 

        return true;

    }

}

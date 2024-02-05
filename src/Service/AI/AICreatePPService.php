<?php

namespace App\Service;

use Assert\Blank;
use Assert\Email;
use Assert\Length;
use Assert\NotBlank;
use App\Entity\Comment;
use Assert\GreaterThan;
use App\AI\Service\OpenAIService;



use App\Validator\NotContainsUrlOrEmail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;



/**
 * User provides data about her project while conversing with an AI assistant.
 * This service creates a Propon Project Presentation Page with these data.
 */

class AICreatePPService {

    protected $em;
    protected $validator;


    public function __construct(EntityManagerInterface $em, ValidatorInterface $validator)
    {
      
        $this->em = $em;
        $this->validator = $validator;

    }

    /**
     * $discussionMaterial is an array of chatGPT conversation.
     */

    public function createJSON($openAIAPIKey, $discussionMaterial){

        $ai = new OpenAIService;

        $instructionsRow = ['role' => 'system', 'content' => "With the previous conversation material, create a json output. First json key is named 'description' : it contains a 3 paragraphs text describing the project. Second json key is named 'qas': it contains an arary of questions and answers people could ask about the project. Please respond with your analysis directly in JSON format
        (without using Markdown code blocks or any other formatting)."];

        $discussionMaterial[] = $instructionsRow;

        return dd($ai->getDiscussionAnswer($openAIAPIKey, "gpt-4", $discussionMaterial));

    }

    /*
    / Turn a PP json representation into a propon pp presentation db object and save it.
    */
    public function createPP($openAIAPIKey, $discussionMaterial){

        $jsonPP = $this->createJSON($openAIAPIKey, $discussionMaterial);

        //treat jsibPP into an actual db PP

    }











    public function validateComment(Comment $comment, $formTimeLoaded, $honeyPot){

        $errors = null;

        // check if comment creation time is not too short (spaming)

        $commentCreationApproximateTimespan = time() - $formTimeLoaded;

        $constraints = [

            new Assert\GreaterThan(['value'=> 5, 'message' => 'Veuillez patienter quelques secondes avant d\'ajouter un commentaire']),
            
        ];

        
        // use the validator to validate the value
        $errors = $this->validator->validate(
            $commentCreationApproximateTimespan,
            $constraints
        ); 

       
        if ($errors->count() > 0) {

            return  $errors[0]->getMessage();
                    
        }


        // check if honey pot is filled

        $constraints = [

            new Assert\Blank(['message' => 'Ce champ devrait être nul']),
            
        ];

        // use the validator to validate the value
        $errors = $this->validator->validate(
            $honeyPot,
            $constraints
        ); 

       
        if ($errors->count() > 0) {

            return  $errors[0]->getMessage();
                    
        }
        


        // check if comment is not empty

        $constraints = [

            new Assert\NotBlank(['message' => 'Veuillez remplir ce champ']),
            new NotContainsUrlOrEmail(),
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

        $userComments = $comment->getUser()->getComments();

        if(!$userComments->isEmpty()){

            $userLastCommentTimespan = time() - $userComments->first()->getCreatedAt()->getTimestamp();

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

        }

        return true;

    }

}

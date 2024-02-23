<?php

namespace App\Service\AI;

use Assert\Blank;
use Assert\Email;
use Assert\Length;
use Assert\NotBlank;
use App\Entity\Comment;
use Assert\GreaterThan;
use App\AI\Service\OpenAIService;
use App\Service\CreatePPService;
use App\Validator\NotContainsUrlOrEmail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints as Assert;



/**
 * User provides data about her project while conversing with an AI assistant.
 * This service creates a Propon Project Presentation Page with these data.
 */

class AICreatePPService {

    protected $em;
    protected $validator;


    public function __construct(EntityManagerInterface $em)
    {
      
        $this->em = $em;

    }


    /**
    * $discussionMaterial is a chatGPT conversation structured as an array.
    */
    public function createPPDataArray($openAIAPIKey, $discussionMaterial){

        $ai = new OpenAIService;

        $instructionsRow = ['role' => 'system', 'content' => $this->summaryComponentsAIPrompt()];

        $discussionMaterial[] = $instructionsRow;

        $projectPresentationElements = json_decode($ai->getDiscussionAnswer($openAIAPIKey, "gpt-4", $discussionMaterial), true);

        return $projectPresentationElements;

    }


    /**
    * PP summary components ai prompt
    */
    protected function summaryComponentsAIPrompt(){

        $aiPrompt = "With the previous conversation material, create a json output. ";

        // Project Goal

        $aiPrompt .= "a json key is named 'goal' : it contains one sentence summarizing the goal of the project. ";

        // Project Keywords

        $aiPrompt .= "a json key is named 'keywords' : it contains some keywords separated by commas. ";

        // Project Description

        $aiPrompt .= "a json key is named 'description' : it contains a 3 paragraphs text describing the project. "; 

        // Project Questions & Answers (FAQ)

        $aiPrompt .= "a json key is named 'qas': it contains an aray of questions and answers people could ask about the project. Don't forget full stop at the end of each element of this array."; 

        $aiPrompt .= "a json key is named 'imagePrompts': it contains an aray of two prompts representing the project you would give to create images with an ai image generator."; 

        // Prompt Ending

        $aiPrompt .= "Respond with your analysis directly in JSON format (without using Markdown code blocks or any other formatting)."; 

        return $aiPrompt;

    }





}

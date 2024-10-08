<?php

namespace App\Service\AI;

use App\Service\AI\OpenAIService;


/**
 * Context: user has provided data about his project while conversing with an AI assistant.
 * 
 * This service creates a Structured Project Presentation Elements Array given this AI - user conversation.
 * 
 * 2 methods are used here:
 * 
 *  - summaryComponentsAIPrompt: returns a text instruction asking AI to summarise the conversation in a json format
 *  - createPPDataArray: returns a structured array of project presentation elements (ex: project goal; project description). This array is easily exploitable to create an actual Propon Project Presentation Page stored in DB (done with createPPService).
 * 
*/

class AICreatePPMaterialService {

    /**
     * Returns a structured array of project presentation elements (ex: project goal; project description).
     * 
    * $discussionMaterial is a user - AI chatGPT conversation formatted as an array the way Open AI wants to receive it.
    */
    public function createPPDataArray($openAIAPIKey, $discussionMaterial){

        $ai = new OpenAIService;
        
        //Creating a OPEN AI FORMATTED CONVERSATION ROW that asks AI to create a formatted project presentation json summary
        $instructionsRow = ['role' => 'system', 'content' => $this->summaryComponentsAIPrompt()];//$this->summaryComponentsAIPrompt() returns the instruction to create this json

        $discussionMaterial[] = $instructionsRow;//COMPLETES the ai - user conversation OPEN AI FORMATTED ARRAY with our just above formatted instruction

        //Getting the AI json output + converting it into a PHP array. As a result we get a project presentation ELEMENTS PHP array 

        $projectPresentationElements = json_decode($ai->getDiscussionAnswer($openAIAPIKey, "gpt-3.5-turbo-0125", $discussionMaterial), true);

        /* Adding some parameters to the newly created project presentation ELEMENTS array*/

        //we consider that this AI generated project presentation has to be admin validated
        $projectPresentationElements["isAdminValidated"] = true;

        //we want to disable user can receive private messages by default
        $projectPresentationElements["privateMessagesActivation"] = false;

        return $projectPresentationElements;

    }


    /**
     * Returns a text instruction that asks AI to create a json output containing some basic project presentation elements (ex: project goal; project description; etc)
    * 
    */
    protected function summaryComponentsAIPrompt(){

        $aiPrompt = "With the previous conversation material, create a json output. For json values, you use french language. The content should not emphasis over project presentation modalities, instead it should focus over project goal.";

        // Project Goal

        $aiPrompt .= "a json key is named \"goal\" : it contains one sentence summarizing the goal of the project and begins with a verb (not something like \"Le but du projet est\"). ";

        // Project Keywords

        $aiPrompt .= "a json key is named 'keywords' : it contains some keywords separated by commas. ";

        // Project Description

        $aiPrompt .= "a json key is named 'description' : it contains 2 or 3 paragraphs of text describing the project. Use html <p> tag to separate these paragraphs."; 

        // Project Questions & Answers (FAQ)

        /* $aiPrompt .= "a json key is named 'qas': it contains an aray of questions and answers people could ask about the project. Each question is named 'question', each answer is named 'answer'. Don't forget full stop at the end of each element of this array."; */

        // Project Images (imaginary representations further created by an ai image creation tool)

        $aiPrompt .= "a json key is named 'imagePrompts': it contains an aray of two prompts representing the project you would give to create images with an ai image generator."; 

        // Prompt Ending

        $aiPrompt .= "Respond with your analysis directly in JSON format (without using Markdown code blocks or any other formatting)."; 

        return $aiPrompt;

    }





}

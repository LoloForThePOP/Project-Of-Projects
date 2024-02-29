<?php

namespace App\Service\AI;

use OpenAI;



class OpenAIService {

    public function getDiscussionAnswer($apiKey, $model, $messages){

        $ia = OpenAI::client($apiKey);

        $response = $ia->chat()->create([
            'model' => $model,
            'messages' => $messages,
        ]);
        
        
        $response->toArray();
        
        return $response['choices'][0]['message']['content'];

    }

    
/* 
    public function answer($model, $prompt){

        $result = $this->ia->completions()->create([
            'model' => $model,
            'prompt' => $prompt,
        ]);

        return $result['choices'][0]['text'];

    } */



}

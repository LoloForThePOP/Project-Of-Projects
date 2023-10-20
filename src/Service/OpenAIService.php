<?php

namespace App\Service;

use OpenAI;



class OpenAIService {

    protected $client;
    protected $model = "gpt-3.5-turbo-instruct";

    public function __construct($apiKey)
    {
        
        $this->client = OpenAI::client($apiKey);

    }

    public function answer($prompt){

        $result = $this->client->completions()->create([
            'model' => $this->model,
            'prompt' => $prompt,
        ]);

        return $result['choices'][0]['text'];

    }



}

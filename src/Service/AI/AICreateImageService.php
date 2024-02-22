<?php

namespace App\Service\AI;

use App\Service\ImageService;
use OpenAI;



class AICreateImageService {

    protected $apiKey;

    public function __construct($apiKey){

        $this->apiKey = $apiKey;

    }

    protected function createImage($prompt){

        $ia = OpenAI::client($this->apiKey);
        
        $response = $ia->images()->create([
            'model' => 'dall-e-3',
            'prompt' => $prompt,
            'size' => "1024x1024",
            'n' => 1,
            'response_format' => 'url',
        ]);
        
        $response->created;
            
        foreach ($response->data as $data) {
            $data->url;
            $data->b64_json; // null
        }
            
        $response->toArray(); 

        return $imageUrl = $response["data"][0]["url"];
    
    }

    public function ttt(){

        $imageService = new ImageService();

        $generatedImagePath = $imageService->saveImageFromUrl($imageUrl);
        $imagesPathsArray = $imageService->splitImage($generatedImagePath);
    }



}

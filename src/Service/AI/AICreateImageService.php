<?php

namespace App\Service\AI;

use App\Service\ImageService;
use OpenAI;



class AICreateImageService {

    protected $apiKey;

    public function __construct($apiKey){

        $this->apiKey = $apiKey;

    }

    /**
     * Return image url
     */
    public function createImage($prompt){

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

        return $response["data"][0]["url"];
    
    }



    /**
    * Allow to store an image to a specific folder (ex : public/ia-generated-logos)
    * Return image name with extension
    */
    public function saveImageFromUrlToPath($imageUrl, $targetPath, $imageName = null){
                
        if (!file_exists($targetPath)) 
        {
            mkdir($targetPath, 0777, true);
        }

        if ($imageName === null) {
            $imageName = uniqid();
        }

        $imageNameWithExtension = $imageName.".png";

        $imagePathWithName = $targetPath."/".$imageNameWithExtension;

        copy($imageUrl , $imagePathWithName);

        return $imageNameWithExtension;

    }




}

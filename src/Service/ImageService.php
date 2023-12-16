<?php

namespace App\Service;

use \Gumlet\ImageResize;


class ImageService {


    const CREATED_LOGOS_STORAGE_PATH = 'public/media/uploads/created_logos/';

    

    public function __construct()
    {
        
        

    }


    /**
    * Allow to store an image to a specific folder
    * Return image name
    */

    public function saveImageFromUrl($imageUrl){
                
        if (!file_exists('public/ia-generated-logos')) 
        {
            mkdir('public/ia-generated-logos', 0777, true);
        }

        $imagePath="public/ia-generated-logos/newImage.png";

        copy($imageUrl , $imagePath);

        return $imagePath;

    }


    /**
    * Allow to split an image into 4 images
    * Creates these images and return arrray of image path
    */


    function splitImage($image_path)
    {
        $src = imagecreatefrompng($image_path);
    
        $size = getimagesize($image_path);
        $part_width = $size[0] / 2;
        $part_height = $size[1] / 2;
    
        $output_images = array();
        for ($i = 0; $i < 2; ++$i) {
            for ($j = 0; $j < 2; ++$j) {
                $img = imagecreatetruecolor($part_width, $part_height);
    
                imagecopyresized(
                    $img, 
                    $src, 
                    0, 
                    0, 
                    $j * $part_width, 
                    $i * $part_height, 
                    $part_width,
                    $part_height, 
                    $part_width, 
                    $part_height
                );
    
                // You can choose a path to save splitted images
                $output_path = 'public/ia-generated-logos/splitted_' . $i . $j . '.png';
                imagepng($img, $output_path);
                imagedestroy($img);
    
                $output_images[] = $output_path;
            }
        }
        imagedestroy($src);
    
        return $output_images;
    }

 

}
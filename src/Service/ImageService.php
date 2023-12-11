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

    public function saveLogoImage($imageUrl){

/*         //set folder to save the image
        $uploadDir = ImageService::CREATED_LOGOS_STORAGE_PATH;
        //Generate filename
        $filename = $uploadDir.md5(time()).'_.png';
        //create the image using the image
        file_put_contents($filename, file_get_contents($imageUrl));
        //return the image to display in main page
        return $filename; */

        
if (!file_exists('public/folder_name')) 
{
    mkdir('public/folder_name', 0777, true);
}
$img_path="public/folder_name/S2_pc.png";
copy($imageUrl , $img_path);
dd ($img_path);

    }

 

}

<?php

namespace App\Service;

use App\Entity\Slide;
use App\Entity\PPBase;
use App\Entity\Persorg;
use \Gumlet\ImageResize;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class ImageResizer {

    protected $uploaderHelper;

    public function __construct(UploaderHelper $uploaderHelper)
    {
        
        $this->uploaderHelper = $uploaderHelper;

    }


    /**
    * Allow to modify an image according to its category (for example : project logo; teammate picture; presentation slide; ...)
    */

    public function edit ($imageEntity){

        
        $imagePath = substr($this->uploaderHelper->asset($imageEntity),1);

        $imageExt = strtolower(substr($imagePath, strrpos($imagePath, '.') + 1));

        //file extension below are not resized due to gumlet limitations

        if ($imageExt == 'svg') {

            return false;
            
        }

        $image = new ImageResize($imagePath);
        $image->quality_jpg = 100;

        if ($imageEntity instanceof Slide) {
            $image->resizeToBestFit(700, 700);
        }

        //PPBase instance means image is a project logo, persorg instance means image is a persorg or organisation avatar
        elseif ($imageEntity instanceof PPBase or $imageEntity instanceof Persorg){

            $image->resizeToBestFit(250, 250);
            
        }

        $image->save($imagePath);





        

    }




}

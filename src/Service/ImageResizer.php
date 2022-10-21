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

    public function edit ($imageEntity, ?string $fieldName = null){
       
        $imagePath = substr($this->uploaderHelper->asset($imageEntity, $fieldName), 1);
        
        if ($imagePath != false) {

            $imageExt = strtolower(substr($imagePath, strrpos($imagePath, '.') + 1));

            //file extension below are not resized due to gumlet limitations

            if ($imageExt == 'svg') {

                return false;
                
            }

            $image = new ImageResize($imagePath);
            $image->quality_jpg = 100;
            $image->quality_webp = 6;
            $image->quality_png = 6;

            if ($imageEntity instanceof Slide) {
                $image->resizeToBestFit(900, 900);
            }

            //PPBase instance means image is a project logo, or thumbnail, 
            //persorg instance means image is a persorg or organisation avatar
            //so in any case, we reduce image width
            elseif ($imageEntity instanceof PPBase or $imageEntity instanceof Persorg){

                $image->resizeToBestFit(340, 340);
                
            }

            $image->save($imagePath);

        }

    }


}

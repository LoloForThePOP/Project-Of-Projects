<?php

namespace App\Service;

use App\Entity\Slide;
use App\Entity\PPBase;
use App\Entity\Persorg;
use \Gumlet\ImageResize;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;


/**
 * Allows to resize images in order to reduce storage.
 * 
 * Uses \Gumlet\ImageResize to do resize treatments (https://github.com/gumlet/php-image-resize) (PHP library to resize, scale and crop images).
 * 
 */
class ImageResizer {

    protected $uploaderHelper; //Allows to retrieve the path of an image given the entity name containing this image (ex: if an image is an attribute of a slide entity, the uploader helper will give us the path of this image) (note: we must precise the attribute name as a second argument if the entity contains several image attributes) (from Vich\UploaderBundle, a symfony bundle that manage file uploads).

    public function __construct(UploaderHelper $uploaderHelper)
    {
        
        $this->uploaderHelper = $uploaderHelper;

    }


    /**
    * 
    * Allow to resize an image according to its category (for example : project logo; teammate picture; presentation slide; ...)
    *
    * Params: 
    *
    *   - $imageEntity: the entity containing the image attribute (ex: a Slide entity can contain an image describing a project (this entity can also contain a caption and some other attributes)).
    *   - $fieldName: some entities contains several image attributes (ex: a News entity can contain several images). In this case to proceed to a specific image we must remove ambiguity so we precise the fieldName (attribute name) of the entity involved (ex: entity: News; fieldName: image2 means I want to proceed on image 2 from a News entity)
    *
    */
    public function edit($imageEntity, ?string $fieldName = null){
       
        $imagePath = substr($this->uploaderHelper->asset($imageEntity, $fieldName), 1);//getting image path to then treat this image
        
        if ($imagePath != false) { // If an image path is retrieved

            //getting file extension (treatments depends on file extension)

            $imageExt = strtolower(substr($imagePath, strrpos($imagePath, '.') + 1));


            if ($imageExt == 'svg') {// Due to Gumlet limitations we can't proceed to reduce svg file weights.

                return false;
                
            }

            $image = new ImageResize($imagePath); // Running a Gumlet library instance
            
            //Setting some image quality compression we apply depending on file extension

            $image->quality_jpg = 100; // from 0 (worst quality, smaller file) to 100 (best quality, biggest file)
            $image->quality_webp = 6; // ??? to be checked ??? ranges from 0 (worst quality, smaller file) to 100 (best quality, biggest file). 
            $image->quality_png = 6; //from (no compression) to 9 (highest compression, worst quality).

            if ($imageEntity instanceof Slide) {// If image is an attribute of a Slide entity

                $image->resizeToBestFit(900, 900);//image is resized to fit in a 900 * 900 square without other alteration
            }

            //
            elseif ($imageEntity instanceof PPBase or $imageEntity instanceof Persorg){// If image is an attribute of a PPBase entity (ex: image is a project logo; a project presentation thumbnail) or if image is an attribute of a Persorg entity (ex: image is a person avatar)

                $image->resizeToBestFit(340, 340); // image is resized to fit in a 340 * 340 square without other alteration
                
            }

            $image->save($imagePath); //saving image changes

        }

    }

}

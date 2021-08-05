<?php

namespace App\Service;

use App\Entity\PPBase;
use Doctrine\ORM\EntityManagerInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class CacheThumbnail {

    protected $uploaderHelper;

    public function __construct(EntityManagerInterface $manager, UploaderHelper $uploaderHelper)
    {
        $this->manager = $manager;
        $this->uploaderHelper = $uploaderHelper;

    }

    /**
    * Cache presentation thumbnail path in entity PPBase $cache.
    * We try to find a subsitute thumbnail (= automatic thumbnail) if no one is formally provided.
    * Here is priority order : customThumbnail (= formal thumbnail provided by user); presentation logo; presentation first slide. If none of these items exists, we set null and will display a default one letter thumbnail.
    * Thumbnail path is store in PPBase entity, $cache['thumbnail'] property.
    */

    public function cacheThumbnail (PPBase $presentation){

        $path = null;

        if ($presentation->getLogo() !== null) {
            $path = $this->uploaderHelper->asset($presentation, 'logoFile');

        }

        elseif (! $presentation->getSlides()->isEmpty()) {

            $slide = $presentation->getSlides()->first();

            if($slide->getType()=='image'){

                $path = $this->uploaderHelper->asset($slide, 'file');

            }

            elseif($slide->getType()=='youtube_video'){

                $path = "https://img.youtube.com/vi/".$slide->getAddress()."/mqdefault.jpg";

            }

        }

        $presentation->setCacheItem('thumbnail', $path);

        $this->manager->flush();

    }




}

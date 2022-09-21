<?php

namespace App\Service;

use App\Entity\Slide;
use App\Entity\PPBase;
use Gumlet\ImageResize;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpKernel\KernelInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class CacheThumbnail {

    protected $uploaderHelper;
    protected $imagineCacheManager;
    protected $kernel;

    public function __construct(EntityManagerInterface $entityManager, UploaderHelper $uploaderHelper, KernelInterface $kernel, CacheManager $imagineCacheManager)
    {
        $this->entityManager = $entityManager;
        $this->uploaderHelper = $uploaderHelper;
        $this->imagineCacheManager = $imagineCacheManager;
        $this->kernel = $kernel;

    }

    /**
    * Cache presentation thumbnail path in entity PPBase $cache.
    * We try to find an image we use as an "automatic thumbnail" if no one is formally provided.
    * Here is priority order : customThumbnail (= formal thumbnail provided by user); presentation first slide; presentation logo. If none of these items exists, we set null and we'll display a default one letter thumbnail.
    * Thumbnail path is store in PPBase entity, $cache['thumbnail'] property.
    */

    public function cacheThumbnail (PPBase $presentation){

        $filter = 'standard_thumbnail_md_test'; // The liip_imagine bundle filter we use to manipulate the image we want to use as a thumbnail (see liip_imagine.yaml)

        $previousThumbnailParentImagePath = $presentation->getCacheItem('thumbnailParentImageAddress');
        $newThumbnailParentImagePath = null;

        if (! $presentation->getSlides()->isEmpty()) {

            $slide = $presentation->getSlides()->first();

            if($slide->getType()=='image'){

                $newThumbnailParentImagePath = $this->uploaderHelper->asset($slide, 'file');

                $presentation->setCacheItem('thumbnailAddress', $this->resolveThumbnailPath($newThumbnailParentImagePath, $filter));

            }

            elseif($slide->getType()=='youtube_video'){

                $newThumbnailParentImagePath = "https://img.youtube.com/vi/".$slide->getAddress()."/mqdefault.jpg";

                $presentation->setCacheItem('thumbnailAddress', $newThumbnailParentImagePath);

                $presentation->setCacheItem('thumbnailParentImageAddress', null);

            }

        }
       
        elseif ($presentation->getLogo() !== null) {

            $newThumbnailParentImagePath = $this->uploaderHelper->asset($presentation, 'logoFile');

            $this->manageThumbnail('resolve', $newThumbnailParentImagePath, $filter);

            $presentation->setCacheItem('thumbnailAddress', $this->resolveThumbnailPath($newThumbnailParentImagePath, $filter));

        }

        //dd($presentation);
        //dd($newThumbnailParentImagePath);

        //case we have a new image to use as a thumbnail: we remove the old one

        if ($newThumbnailParentImagePath != $previousThumbnailParentImagePath) {

            //$this->manageThumbnail('remove', $previousThumbnailParentImagePath, $filter);
            
            $presentation->setCacheItem('thumbnailParentImageAddress', $newThumbnailParentImagePath);
            
        }

        if ($newThumbnailParentImagePath == null){

            $presentation->setCacheItem('thumbnailAddress', null);

        }

        $this->entityManager->flush();

    }

    public function resolveThumbnailPath($parentImagePath, $filter){

        return $this->imagineCacheManager->getBrowserPath($parentImagePath, $filter);

    }

    /**
     * From an image path, allow to programaticaly create or remove a thumbnail using a specific liip omagine filter
     */

    public function manageThumbnail($action, $parentImagePath, $filter)
    {
        $kernel = $this->kernel;
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => "liip:imagine:cache:".$action,
            // (optional) define the value of command arguments 
            'paths' => [$parentImagePath],
            // (optional) pass options to the command
            '--filter' => [$filter],
           
        ]);

        // You can use NullOutput() if you don't need the output
        $output = new NullOutput();
        $application->run($input, $output);

    }



}
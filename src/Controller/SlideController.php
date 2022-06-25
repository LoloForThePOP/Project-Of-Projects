<?php

namespace App\Controller;

use App\Entity\Slide;
use App\Entity\PPBase;
use App\Service\TreatItem;
use App\Form\ImageSlideType;
use App\Service\AssessQuality;
use App\Form\VideoSlideType;
use App\Service\CacheThumbnail;
use App\Repository\SlideRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SlideController extends AbstractController
{
    /**
     * @Route("/projects/{stringId}/slides/order", name="order_slides", priority="3")
     */
    public function order(PPBase $presentation): Response
    {

        $this->denyAccessUnlessGranted('edit', $presentation);

        return $this->render('project_presentation/edit/slides/order.html.twig', [
            'presentation' => $presentation,
            'stringId' => $presentation->getStringId(),
        ]);

    }
    
    
    /**
     * Allow to update an image slide or its caption
     *  
     * @Route("/projects/{stringId}/slide/update-image/{id_slide}", name="update_image_slide")
     * 
     * @return Response
     */
    public function updateImageSlide (PPBase $pp, $id_slide, SlideRepository $repo, Request $request, EntityManagerInterface $manager, CacheThumbnail $cacheThumbnail){

        $this->denyAccessUnlessGranted('edit', $pp);

        $slide = $repo->findOneById($id_slide);

        $form = $this->createForm(ImageSlideType::class, $slide);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
                             
            $manager->flush();

            $cacheThumbnail->cacheThumbnail($pp);

            $this->addFlash(
                'success',
                "✅ modification effectuée"
            );

            return $this->redirectToRoute('show_presentation', [

                'stringId' => $pp->getStringId(),
                '_fragment' => 'slideshow-struct-container',

            ]);

        }

        return $this->render('project_presentation/edit/slides/update_image.html.twig', [
            'stringId' => $pp->getStringId(),
            'form' => $form->createView(),
            'slide' => $slide,

        ]);

    }


    /**
     * Allow to update a youtube video slide
     * 
     * @Route("/projects/{stringId}/slides/edit-youtube-video/{id_slide}",name="update_youtube_slide")
     * 
     * @return Response
     */
    public function editVideoSlide (PPBase $pp, $id_slide, SlideRepository $repo, Request $request, EntityManagerInterface $manager, CacheThumbnail $cacheThumbnail, TreatItem $specificTreatments) {

        $this->denyAccessUnlessGranted('edit', $pp);

        $slide = $repo->findOneById($id_slide);

        $slide->setFile(null); //otherwise get a bug (caused by vitch upload ?)

        $form = $this->createForm(VideoSlideType::class, $slide);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $youtubeVideoIdentifier = $specificTreatments->specificTreatments('youtube_video', $form->get('address')->getData());//user might has given a complete youtube video url or just the video identifier. We extract the video identifier in the first case.

            $slide->setAddress($youtubeVideoIdentifier);

            $manager->flush();

            $cacheThumbnail->cacheThumbnail($pp);

            $this->addFlash(
                'success',
                "✅ Modification effectuée"
            );

            return $this->redirectToRoute('show_presentation', [

                'stringId' => $pp->getStringId(),
                '_fragment' => 'slideshow-struct-container',

            ]);

        }

        return $this->render('project_presentation/edit/slides/edit_youtube_video.html.twig', [

            'form' => $form->createView(),
            'stringId' => $pp->getStringId(), 
            'presentation' => $pp,
            'context' => "update",
            
        ]);

    }

    /**
     * Allow to update a slide by redirecting to appropriate method
     * 
     * @Route("/projects/{stringId}/slides/update/{id_slide}",name="update_slide")
     * 
     * @return Response
     */
    public function updateSlide (PPBase $pp, $id_slide, SlideRepository $repo) {

        $this->denyAccessUnlessGranted('edit', $pp);

        $slide = $repo->findOneById($id_slide);

        $slideType = $slide->getType();

        $routeName = null;

        switch ($slideType) {

            case 'image':

                $routeName = 'update_image_slide';
                break;

            case 'youtube_video':

                $routeName = 'update_youtube_slide';
                break;
            
            default:
                break;
        }

        return $this->redirectToRoute($routeName, [

            'stringId' => $pp->getStringId(),
            'presentation'=> $pp, 
            'id_slide' => $id_slide,

        ]);

    }








    /*
    / slides reordering and slides removing
    /
    / see ComponentsController
    /
    */

  

}

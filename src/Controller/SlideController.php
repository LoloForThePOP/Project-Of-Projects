<?php

namespace App\Controller;

use App\Entity\Slide;
use App\Entity\PPBase;
use App\Form\ImageSlideType;
use App\Service\AssessQuality;
use App\Form\AddVideoSlideType;
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
                '_fragment' => 'slides',

            ]);

        }

        return $this->render('project_presentation/edit/slides/update_image.html.twig', [
            'stringId' => $pp->getStringId(),
            'form' => $form->createView(),
            'slide' => $slide,

        ]);

    }

    
    /**
     * Allow to add a youtube video slide into a slideshow
     * 
     * @Route("/projects/{stringId}/slides/add-youtube-slide",name="add_youtube_slide")
     * 
     * @return Response
     */
    public function addYoutubeSlide(PPBase $presentation, Request $request, EntityManagerInterface $manager, CacheThumbnail $cacheThumbnail, AssessQuality $assessQuality)
    {

        $this->denyAccessUnlessGranted('edit', $presentation);

        $videoSlide = new Slide();

        $addVideoForm = $this->createForm(AddVideoSlideType::class, $videoSlide);

        $videoSlide->setType("youtube_video");

        $addVideoForm->handleRequest($request);

        if ($addVideoForm->isSubmitted() && $addVideoForm->isValid()) {

            // count previous slide in order to set new slides positions

            $videoSlide->setPosition(count($presentation->getSlides()));
            $presentation->addSlide($videoSlide);
            $manager->persist($videoSlide);

            $assessQuality->assessQuality($presentation);  

            $cacheThumbnail->cacheThumbnail($presentation);

            $manager->flush();            

            $this->addFlash(
                'success',
                "✅ vidéo ajoutée"
            );

            return $this->redirectToRoute('show_presentation', [

                'stringId' => $presentation->getStringId(),
                '_fragment' => 'slides',

            ]);

        }

        return $this->render('project_presentation/edit/slides/edit_youtube_video.html.twig', [

            'presentation' => $presentation,
            'stringId' => $presentation->getStringId(),
            'form' => $addVideoForm->createView(),
            'context' => 'new',

        ]);
    }


    /**
     * Allow to update a youtube video slide
     * 
     * @Route("/projects/{stringId}/slides/edit-youtube-video/{slide_id}",name="update_youtube_slide")
     * 
     * @return Response
     */
    public function editVideoSlide (PPBase $pp, $slide_id, SlideRepository $repo, Request $request, EntityManagerInterface $manager, CacheThumbnail $cacheThumbnail) {

        $this->denyAccessUnlessGranted('edit', $pp);

        $slide = $repo->findOneById($slide_id);

        $slide->setFile(null); //otherwise get a bug (caused by vitch upload ?)

        $form = $this->createForm(AddVideoSlideType::class, $slide);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $manager->flush();

            $cacheThumbnail->cacheThumbnail($pp);

            $this->addFlash(
                'success',
                "✅ Modification effectuée"
            );

            return $this->redirectToRoute('show_presentation', [

                'stringId' => $pp->getStringId(),
                '_fragment' => 'slides',

            ]);

        }

        return $this->render('project_presentation/edit/slides/edit_youtube_video.html.twig', [

            'form' => $form->createView(),
            'stringId' => $pp->getStringId(), 
            'presentation' => $pp,
            'context' => "update",
            
        ]);

    }

    /*
    / slides reordering and slides removing
    /
    / see ComponentsController
    /
    */

  

}

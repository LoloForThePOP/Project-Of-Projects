<?php

namespace App\Controller;

use App\Entity\Slide;
use App\Entity\PPBase;
use App\Form\ImageSlideType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\AddVideoSlideType;

class SlideController extends AbstractController
{
    /**
     * @Route("/projects/{stringId}/slides/", name="manage_slides")
     */
    public function manage(PPBase $presentation, Request $request, EntityManagerInterface $manager): Response
    {

        $this->denyAccessUnlessGranted('edit', $presentation);

        // add image slide form (add youtube slide treated in another method of this controller)

        $imageSlide = new Slide();

        $imageSlide->setType('image');

        $addImageForm = $this->createForm(ImageSlideType::class, $imageSlide);

        $addImageForm->handleRequest($request);

        if ($addImageForm->isSubmitted() && $addImageForm->isValid()) {

            $imageSlide->setPosition(count($presentation->getSlides()));

            $presentation->addSlide($imageSlide);

            $manager->persist($imageSlide);
            $manager->persist($presentation);
            $manager->flush();

            $this->addFlash(
                'success',
                "✅ Image ajoutée"
            );

            return $this->redirectToRoute(
                'manage_slides',
                [

                    'stringId' => $presentation->getStringId(),

                ]
            );
        }

        return $this->render('project_presentation/edit/slides/manage.html.twig', [
            'presentation' => $presentation,
            'stringId' => $presentation->getStringId(),
            'addImageForm' => $addImageForm->createView(),
        ]);
    }


    /**
     * Allow to add a youtube video slide into a slideshow
     * 
     * @Route("/projects/{stringId}/slides/edit-youtube-slide",name="add_youtube_slide")
     * 
     * @return Response
     */
    public function addYoutubeSlide(PPBase $presentation, Request $request, EntityManagerInterface $manager)
    {

        $this->denyAccessUnlessGranted('edit', $presentation);

        $videoSlide = new Slide();

        $addVideoForm = $this->createForm(AddVideoSlideType::class, $videoSlide);

        $videoSlide->setType("youtube_video");

        $addVideoForm->handleRequest($request);

        if ($addVideoForm->isSubmitted() && $addVideoForm->isValid()) {

            // count previous slide in order to set new slides positions

            $videoSlide->setPosition(count($presentation->getSlides()));

            $videoSlide->setPresentation($presentation);
            $manager->persist($videoSlide);

            $manager->persist($presentation);
            $manager->flush();

            $this->addFlash(
                'success',
                "✅ vidéo ajoutée"
            );

            return $this->redirectToRoute('manage_slides', [

                'stringId' => $presentation->getStringId(),
            ]);
        }

        return $this->render('project_presentation/edit/slides/add_youtube_video.html.twig', [

            'presentation' => $presentation,
            'stringId' => $presentation->getStringId(),
            'addVideoForm' => $addVideoForm->createView(),

        ]);
    }
}

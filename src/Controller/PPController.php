<?php

namespace App\Controller;

use App\Entity\Slide;
use App\Entity\PPBase;
use App\Entity\Document;
use App\Form\PPBaseType;
use App\Form\WebsiteType;
use App\Form\DataListType;
use App\Form\DocumentType;
use App\Form\ImageSlideType;
use App\Form\BusinessCardType;
use App\Form\CreatePresentationType;
use App\Service\TreatOtherComponentItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PPController extends AbstractController
{


    /**
     * Allow to create a project presentation
     * 
     * @Route("/projects/create", name="create_presentation")
     * 
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $manager)
    {

        $this->denyAccessUnlessGranted('ROLE_USER');

        $presentation = new PPBase();

        $form = $this->createForm(
            CreatePresentationType::class,
            $presentation,
            array(

                // Time protection
                'antispam_time'     => true,
                'antispam_time_min' => 4,
                'antispam_time_max' => 3600,
            )
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $presentation->setCreator($this->getUser());

            $manager->persist($presentation);
            $manager->flush();

            $this->addFlash(
                'success',
                '✅ La présentation du projet a été créée. <br> Vous pouvez maintenant ajouter toutes les informations que vous désirez présenter.'
            );

            return $this->redirectToRoute('show_presentation', [
                "stringId" => $presentation->getStringId(),
            ]);
        }

        return $this->render('project_presentation/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * Allow to Display a Project Presentation Page
     * 
     * @Route("/projects/{stringId}/", name="show_presentation")
     * 
     * @return Response
     */
    public function show(PPBase $presentation, Request $request, TreatOtherComponentItem $specificTreatments, EntityManagerInterface $manager)
    {

        $this->denyAccessUnlessGranted('view', $presentation);

        $user = $this->getUser();

        if ($this->isGranted('edit', $presentation)) {

            $addWebsiteForm = $this->createForm(WebsiteType::class);
            $addWebsiteForm->handleRequest($request);
            if ($addWebsiteForm->isSubmitted() && $addWebsiteForm->isValid()) {

                $componentItem = $addWebsiteForm->getData();

                $componentItem = $specificTreatments->specificTreatments('websites', $componentItem);

                $presentation->addOtherComponentItem('websites', $componentItem);

                $manager->flush();

                $this->addFlash(
                    'success',
                    "✅ Ajout effectué"
                );

                return $this->redirectToRoute(
                    'show_presentation',
    
                    [
    
                        'stringId' => $presentation->getStringId(),
    
                    ]

                );

            }

            $addBusinessCardForm = $this->createForm(BusinessCardType::class);
            $addBusinessCardForm->handleRequest($request);
            if ($addBusinessCardForm->isSubmitted() && $addBusinessCardForm->isValid()) {

                $componentItem = $addBusinessCardForm->getData();

                $componentItem = $specificTreatments->specificTreatments('businessCards', $componentItem);

                $presentation->addOtherComponentItem('businessCards', $componentItem);

                $manager->flush();

                $this->addFlash(
                    'success',
                    "✅ Ajout effectué"
                );

                return $this->redirectToRoute(
                    'show_presentation',
    
                    [
    
                        'stringId' => $presentation->getStringId(),
    
                    ]

                );

            }

            $addDataListElemForm = $this->createForm(DataListType::class);
            $addDataListElemForm->handleRequest($request);
            if ($addDataListElemForm->isSubmitted() && $addDataListElemForm->isValid()) {

                $componentItem = $addDataListElemForm->getData();

                $componentItem = $specificTreatments->specificTreatments('dataList', $componentItem);

                $presentation->addOtherComponentItem('dataList', $componentItem);

                $manager->flush();

                $this->addFlash(
                    'success',
                    "✅ Ajout effectué"
                );

                return $this->redirectToRoute(
                    'show_presentation',
    
                    [
    
                        'stringId' => $presentation->getStringId(),
    
                    ]

                );

            }

            $document=new Document();
            $addDocumentForm = $this->createForm(DocumentType::class, $document);
            $addDocumentForm->handleRequest($request);
            if ($addDocumentForm->isSubmitted() && $addDocumentForm->isValid()) {

                $document->setPresentation($presentation);

                $manager->persist($document);
    
                $manager->flush();
    
                $this->addFlash(
                    'success',
                    "✅ Ajout effectué"
                );

                return $this->redirectToRoute(
                    'show_presentation',
    
                    [
    
                        'stringId' => $presentation->getStringId(),
    
                    ]

                );

            }

            $imageSlide = new Slide();
            $imageSlide->setType('image');
            $addImageForm = $this->createForm(ImageSlideType::class, $imageSlide);
            $addImageForm->handleRequest($request);
            if ($addImageForm->isSubmitted() && $addImageForm->isValid()) {
                $imageSlide->setPosition(count($presentation->getSlides()));

                $presentation->addSlide($imageSlide);

                $manager->persist($imageSlide);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "✅ Image ajoutée"
                );

                return $this->redirectToRoute(
                    'show_presentation',
                    [

                        'stringId' => $presentation->getStringId(),

                    ]
                );
            }

            return $this->render('/project_presentation/show.html.twig', [
                'presentation' => $presentation,
                'stringId' => $presentation->getStringId(),
                'addWebsiteForm' => $addWebsiteForm->createView(),
                'addDataListElemForm' => $addDataListElemForm->createView(),
                'addBusinessCardForm' => $addBusinessCardForm->createView(),
                'addDocumentForm' => $addDocumentForm->createView(),
                'addImageForm' => $addImageForm->createView(),
            ]);

        }


        return $this->render('/project_presentation/show.html.twig', [
            'presentation' => $presentation,
            'stringId' => $presentation->getStringId(),
        ]);
    }

    /**
     * Allow to Display a Project Presentation Edition Menu
     * 
     * @Route("/projects/{stringId}/edit/menu", name="edit_pp_menu", priority="5")
     * 
     * @return Response
     */
    public function showMenu(PPBase $presentation)
    {

        $user = $this->getUser();

        $this->denyAccessUnlessGranted('edit', $presentation);

        return $this->render('/project_presentation/edit/menu.html.twig', [
            'presentation' => $presentation,
            'stringId' => $presentation->getStringId(),
        ]);
    }




    /**
     * Allow to edit pp title; goal; keywords & logo
     * 
     * @Route("/projects/{stringId}/edit/", name="edit_pp_base")
     * 
     * @return void
     */
    public function editBase(PPBase $presentation, Request $request, EntityManagerInterface $manager)
    {

        $this->denyAccessUnlessGranted('edit', $presentation);

        $form = $this->createForm(PPBaseType::class, $presentation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager->persist($presentation);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les modifications ont été enregistrées ✅"
            );

            return $this->redirectToRoute('show_presentation', [
                'stringId' => $presentation->getStringId(),
            ]);
        }


        return $this->render('project_presentation/edit/title_goal_logo/edit.html.twig', [
            'presentation' => $presentation,
            'stringId' => $presentation->getStringId(),
            'form' => $form->createView(),
        ]);
    }
}

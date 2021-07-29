<?php

namespace App\Controller;

use App\Entity\Slide;
use App\Entity\PPBase;
use App\Entity\Persorg;
use App\Entity\Document;
use App\Form\PPBaseType;
use App\Form\PersorgType;
use App\Form\WebsiteType;
use App\Form\DataListType;
use App\Form\DocumentType;
use App\Form\ImageSlideType;
use App\Form\BusinessCardType;
use App\Form\QuestionAnswerType;
use App\Entity\ContributorStructure;
use App\Form\CreatePresentationType;
use App\Form\ContributorStructureType;
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
                        '_fragment' => 'pp-websites'
    
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
                        '_fragment' => 'businessCards',
    
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
                        '_fragment' => 'dataList',

    
                    ]

                );

            }

            $addQAForm = $this->createForm(QuestionAnswerType::class);
            $addQAForm->handleRequest($request);
            if ($addQAForm->isSubmitted() && $addQAForm->isValid()) {

                $componentItem = $addQAForm->getData();

                $componentItem = $specificTreatments->specificTreatments('questionsAnswers', $componentItem);

                $presentation->addOtherComponentItem('questionsAnswers', $componentItem);

                $manager->flush();

                $this->addFlash(
                    'success',
                    "✅ Ajout effectué"
                );

                return $this->redirectToRoute(
                    'show_presentation',
    
                    [
    
                        'stringId' => $presentation->getStringId(),
                        '_fragment' => 'questionsAnswers',

    
                    ]

                );

            }

            $document= new Document();
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
                        '_fragment' => 'documents',
    
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

                if ($presentation->getSlides()->count()==1) {

                    return $this->redirectToRoute(
                        'show_presentation',
                        [
    
                            'stringId' => $presentation->getStringId(),
                            '_fragment' => 'slides',
    
                        ]
                    );

                }

                else { //more than one slide, user will manage slides positions
                        
                    return $this->redirectToRoute(
                        'manage_slides',
                        [

                            'stringId' => $presentation->getStringId(),
                        ]
                    );

                }

            }

            $newECS = new ContributorStructure();
            $ecsForm = $this->createForm(ContributorStructureType::class, $newECS);
            $ecsForm->handleRequest($request);
            if ($ecsForm->isSubmitted() && $ecsForm->isValid()){

                $newECS->setType('external');

                $newECS->setPresentation($presentation);

                $manager->persist($newECS);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "✅ Partie ajoutée. Vous pouvez maintenant la remplir."
                );

                return $this->redirectToRoute('manage_one_cs', [
                    'stringId' => $presentation->getStringId(),
                    'id_cs' => $newECS->getId(),
                ]);

            }

            $persorg = new Persorg();
            $addPersorgForm = $this->createForm(PersorgType::class, $persorg);
            $addPersorgForm->handleRequest($request);
            if ($addPersorgForm->isSubmitted() && $addPersorgForm->isValid()){

                $parentContributorStructureId = $addPersorgForm->get('parentStuctureId')->getData();

                // check if posted parent structure is really owned by this presentation

                $parentContributorStructure = $this->getDoctrine()->getRepository(ContributorStructure::class)->findOneBy(
                    [
                        
                        "id" => $parentContributorStructureId

                    ]
                );

                if ($presentation == $parentContributorStructure->getPresentation()) {

                    $persorg->setContributorStructure($parentContributorStructure);
                    
                    $manager->persist($persorg);

                    $manager->flush();

                    $this->addFlash(
                        'success',
                        "✅ Ajout effectué"
                    );

                    return $this->redirectToRoute('show_presentation', [
                        'stringId' => $presentation->getStringId(),
                        '_fragment' => 'cs-'.$parentContributorStructureId,
                    ]);

                }
            }

            return $this->render('/project_presentation/show.html.twig', [
                'presentation' => $presentation,
                'stringId' => $presentation->getStringId(),
                'addWebsiteForm' => $addWebsiteForm->createView(),
                'addQAForm' => $addQAForm->createView(),
                'addECSForm' => $ecsForm->createView(),
                'addPersorgForm' => $addPersorgForm->createView(),
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
                "✅ Les modifications ont été enregistrées"
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

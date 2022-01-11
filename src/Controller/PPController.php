<?php

namespace App\Controller;

use App\Entity\Slide;
use App\Service\Slug;
use App\Entity\PPBase;
use App\Entity\Persorg;
use App\Form\TitleType;
use App\Entity\Document;
use App\Form\PPBaseType;
use App\Form\PersorgType;
use App\Form\WebsiteType;
use App\Form\DataListType;
use App\Form\DocumentType;
use App\Form\StringIdType;
use App\Service\TreatItem;
use App\Form\ImageSlideType;
use App\Service\ImageResizer;
use App\Form\BusinessCardType;
use App\Form\DeleteEntityType;
use App\Service\AssessQuality;
use App\Service\CacheThumbnail;
use App\Form\QuestionAnswerType;
use App\Service\RemovePresentation;
use App\Entity\ContributorStructure;
use App\Form\CreatePresentationType;
use Symfony\Component\Form\FormError;
use App\Form\ContributorStructureType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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

            $askForGuidance=$form->get('acceptGuidance')->getData();

            if($askForGuidance=="yes"){
                    
                return $this->redirectToRoute('presentation_helper', [
                    "stringId" => $presentation->getStringId(),                
                    "position" => 0,                
                ]);

            }

            $this->addFlash(
                'success fs-4',
                "✅ La présentation du projet a été créée. <br> 🙋 Si vous avez besoin d'aide, utilisez le bouton d'aide en bas de page."
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
     * Allow to Display or Edit a Project Presentation Page
     * 
     * @Route("/{stringId}/", name="show_presentation", priority=-1)
     * @Route("/projects/{stringId}/", name="long_path_show_presentation")

     * 
     * @return Response
     */
    public function show(PPBase $presentation, Request $request, TreatItem $specificTreatments, EntityManagerInterface $manager, CacheThumbnail $cacheThumbnail, ImageResizer $imageResizer, AssessQuality $assessQuality, Slug $slug)
    {

        $this->denyAccessUnlessGranted('view', $presentation);

        $user = $this->getUser();

        //updating views count
        if($user != $presentation->getCreator()){

            $presentation->setDataItem('viewsCount', $presentation->getDataItem('viewsCount')+1);

            $manager->flush();

        }

        if ($this->isGranted('edit', $presentation)) {

            $addWebsiteForm = $this->createForm(WebsiteType::class);
            $addWebsiteForm->handleRequest($request);
            if ($addWebsiteForm->isSubmitted() && $addWebsiteForm->isValid()) {

                $componentItem = $addWebsiteForm->getData();

                $componentItem = $specificTreatments->specificTreatments('websites', $componentItem);

                $presentation->addOtherComponentItem('websites', $componentItem);

                $manager->flush();

                $this->addFlash(
                    'success fade-out',
                    "✅ Ajout effectué"
                );

                return $this->redirectToRoute(
                    'show_presentation',
    
                    [
    
                        'stringId' => $presentation->getStringId(),
                        '_fragment' => 'websites-struct-container'
    
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
                        '_fragment' => 'businessCards-struct-container',
    
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
                        '_fragment' => 'dataList-struct-container',

    
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
                        '_fragment' => 'questionsAnswers-struct-container',

    
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
                        '_fragment' => 'documents-struct-container',
    
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

                $assessQuality->assessQuality($presentation);  

                $imageResizer->edit($imageSlide);
 
                $cacheThumbnail->cacheThumbnail($presentation);

                $this->addFlash(
                    'success',
                    "✅ Image ajoutée"
                );

                return $this->redirectToRoute(
                    'show_presentation',
                    [

                        'stringId' => $presentation->getStringId(),
                        '_fragment' => 'slideshow-struct-container',

                    ]
                );

            }

            $addLogoForm = $this->createForm(PPBaseType::class, $presentation);
            $addLogoForm->handleRequest($request);
            
            if ($addLogoForm->isSubmitted() && $addLogoForm->isValid()) {

                $manager->flush();

                $imageResizer->edit($presentation);
                $cacheThumbnail->cacheThumbnail($presentation);

                $this->addFlash(
                    'success',
                    "✅ Modification Effectuée"
                );

                return $this->redirectToRoute(
                    'show_presentation',
                    [

                        'stringId' => $presentation->getStringId(),

                    ]
                );

            }

           
            $addTitleForm = $this->createForm(TitleType::class, $presentation);
            $addTitleForm->handleRequest($request);
            
            if ($addTitleForm->isSubmitted() && $addTitleForm->isValid()) {

                $manager->flush();

                $this->addFlash(
                    'success',
                    "✅ Modification Effectuée"
                );

                return $this->redirectToRoute(
                    'show_presentation',
                    [

                        'stringId' => $presentation->getStringId(),

                    ]
                );

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

                    $imageResizer->edit($persorg);

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

            // personalize presentation stringId (= slug)
            $updateStringIdForm = $this->createForm(StringIdType::class, $presentation);
            $suggestedStringId = null;

            if ($presentation->getOverallQualityAssessment() > 1 &&$presentation->getDataItem("validatedStringId") == false) {

                $suggestedStringId = $slug->suggestSlug($presentation);
                $updateStringIdForm->handleRequest($request);

                if ($updateStringIdForm->isSubmitted() && $updateStringIdForm->isValid()){

                    $slugedInput = $slug->slugInput($updateStringIdForm->get('stringId')->getData());

                    $presentation->setStringId($slugedInput);
                    $presentation->setDataItem("validatedStringId", true);

                    $manager->flush();

                    $newPresentationURL = $this->generateUrl('show_presentation', [
                        'stringId' => $presentation->getStringId(),
                    ], UrlGeneratorInterface::ABSOLUTE_URL);

                    $this->addFlash(
                        'success',
                        "👉 L'adresse de votre page de projet est désormais <b>$newPresentationURL</b><br>
                        👉 Pour la copier, partager, ou modifier, utilisez le bouton \"Partager la présentation\" en bas de page."
                    );

                    return $this->redirectToRoute(
                        'show_presentation',
                        [

                            'stringId' => $presentation->getStringId(),
                            '_fragment' => 'flash-messages',

                        ]
                    );
                }

          
            }

            return $this->render('/project_presentation/show.html.twig', [
                'presentation' => $presentation,
                'stringId' => $presentation->getStringId(),
                'suggestedStringId' =>$suggestedStringId,
                'contactUsPhone' => $this->getParameter('app.contact_phone'),
                'addWebsiteForm' => $addWebsiteForm->createView(),
                'addQAForm' => $addQAForm->createView(),
                'addECSForm' => $ecsForm->createView(),
                'addPersorgForm' => $addPersorgForm->createView(),
                'addDataListElemForm' => $addDataListElemForm->createView(),
                'addBusinessCardForm' => $addBusinessCardForm->createView(),
                'addDocumentForm' => $addDocumentForm->createView(),
                'addImageForm' => $addImageForm->createView(),
                'addLogoForm' => $addLogoForm->createView(),
                'addTitleForm' => $addTitleForm->createView(),
                'updateStringIdForm' => $updateStringIdForm->createView(),
            ]);

        }


        return $this->render('/project_presentation/show.html.twig', [
            'presentation' => $presentation,
            'stringId' => $presentation->getStringId(),
            'contactUsPhone' => $this->getParameter('app.contact_phone'),
        ]);
    }



    /**
     * Allow to Delete a Project Presentation Page
     * 
     * @Route("/projects/{stringId}/delete", name="delete_presentation")
     * 
     * @return Response
    */
    public function delete(PPBase $presentation, Request $request, RemovePresentation $deletePresentationService)
    {

        $this->denyAccessUnlessGranted('edit', $presentation);

        $confirmForm = $this->createForm(DeleteEntityType::class);

        $confirmForm->handleRequest($request);

        if ($confirmForm->isSubmitted() && $confirmForm->isValid()){

            $deletePresentationService->removePresentation($presentation);

            $this->addFlash(
                'success',
                "✅ Présentation supprimée."
            );

            return $this->redirectToRoute('homepage', []);

        }

        return $this->render('/project_presentation/delete.html.twig', [

            'presentation' => $presentation,            
            'stringId' => $presentation->getStringId(),
            'form' => $confirmForm->createView(),
            
        ]);

    }


        
    /** 
     * Allow to ajax some presentation settings
     * 
     * @Route("/projects/{stringId}/ajax-set-data", name="ajax_presentation_settings") 
     * 
    */ 

    public function ajaxSetData(Request $request, PPBase $presentation,EntityManagerInterface $manager) {

        $this->denyAccessUnlessGranted('edit', $presentation);

        if ($request->isXmlHttpRequest()) {

            $data = json_decode($request->request->get('data'), true);

            $presentation->setDataItem($data['key'], $data['value']);

            $manager->flush();
          
            return  new JsonResponse(true);

        }

        return  new JsonResponse();

    }
        
    /** 
     * Allow to ajax some presentation settings
     * 
     * @Route("/projects/{stringId}/ajax-set-data-legacy", name="ajax_presentation_settings_legacy") 
     * 
    */ 

    public function ajaxSetDataLegacy(Request $request, PPBase $presentation,EntityManagerInterface $manager) {

        $this->denyAccessUnlessGranted('edit', $presentation);

        if ($request->isXmlHttpRequest()) {

            $settedItem = $request->request->get('settedItem');
            $jsonSwitchState = $request->request->get('switchState');

            $switchState = json_decode($jsonSwitchState, true);

            switch ($settedItem) {

                case 'publish-presentation-switch':
                    
                    $presentation->setIsPublished($switchState);
                    break;

                case 'pm-activation-switch':
                    
                    $presentation->setParameter('arePrivateMessagesActivated', $switchState);
                    break;
                
                default:
                    
                    break;
            }

            $manager->flush();
          
            return  new JsonResponse(true);

        }

        return  new JsonResponse();

    }


    /**
     * Allow to edit pp title; goal & logo
     * 
     * @Route("/projects/{stringId}/edit/", name="edit_pp_base")
     * 
     * @return void
     */
    public function editBase(PPBase $presentation, Request $request, EntityManagerInterface $manager, CacheThumbnail $cacheThumbnail, ImageResizer $imageResizer, AssessQuality $assessQuality)
    {

        $this->denyAccessUnlessGranted('edit', $presentation);

        $form = $this->createForm(PPBaseType::class, $presentation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $assessQuality->assessQuality($presentation);  

            $manager->flush();

            $imageResizer->edit($presentation);
            
            $cacheThumbnail->cacheThumbnail($presentation);
                       

            $this->addFlash(
                'success fade-out',
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


    /**
     * Allow to edit pp stringId
     * 
     * @Route("/projects/{stringId}/edit/string-id", name="edit_pp_string_id")
     * 
     * @return void
     */
    public function editStringId(PPBase $presentation, Request $request, EntityManagerInterface $manager, Slug $slug)
    {

        $this->denyAccessUnlessGranted('edit', $presentation);

        if ($presentation->getOverallQualityAssessment() > 1 &&$presentation->getDataItem("validatedStringId") == true) {
            
            $form = $this->createForm(StringIdType::class, $presentation);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $presentation->setStringId($slug->slugInput($form->get('stringId')->getData()));

                $manager->flush();

                $newPresentationURL = $this->generateUrl('show_presentation', [
                    'stringId' => $presentation->getStringId(),
                ], UrlGeneratorInterface::ABSOLUTE_URL);
                        
                $this->addFlash(
                    'success',
                    "👉 L'adresse de votre page de projet est désormais <b>$newPresentationURL</b><br>
                    👉 Pour la copier, partager, ou modifier, utilisez le bouton \"Partager la présentation\" en bas de page."
                );

                return $this->redirectToRoute('show_presentation', [
                    'stringId' => $presentation->getStringId(),
                    '_fragment' => 'flash-messages'
                ]);
            }
        }

        


        return $this->render('project_presentation/edit/title_goal_logo/_update_string_id_form.html.twig', [
            'presentation' => $presentation,
            'stringId' => $presentation->getStringId(),
            'form' => $form->createView(),
        ]);
    }



}

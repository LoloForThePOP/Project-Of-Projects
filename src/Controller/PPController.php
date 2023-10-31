<?php

namespace App\Controller;

use App\Entity\Like;
use App\Entity\User;
use App\Entity\Slide;
use App\Service\Slug;
use App\Entity\PPBase;
use App\Form\NewsType;
use App\Entity\Comment;
use App\Entity\Persorg;
use App\Entity\Document;
use App\Form\PPBaseType;
use App\Form\CommentType;
use App\Form\PersorgType;
use App\Form\WebsiteType;
use App\Form\DocumentType;
use App\Form\MiscDataType;
use App\Form\StringIdType;
use App\Service\TreatItem;
use App\Service\LiveSavePP;
use App\Form\ImageSlideType;
use App\Form\VideoSlideType;
use App\Service\ImageResizer;
use App\Form\BusinessCardType;
use App\Form\DeleteEntityType;
use App\Service\AssessQuality;
use App\Service\MailerService;
use App\Service\CacheThumbnail;
use App\Form\QuestionAnswerType;
use App\Form\CustomThumbnailType;
use App\Form\RegistrationFormType;
use App\Repository\LikeRepository;
use App\Service\RemovePresentation;
use App\Entity\ContributorStructure;
use App\Form\CreatePresentationType;
use Symfony\Component\Form\FormError;
use App\Form\ContributorStructureType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Url;
use App\Form\WithoutUsernameRegistrationFormType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PPController extends AbstractController
{


    /**
     * Allow to create a project presentation
     * 
     * @Route("/projects/create", name="create_presentation")
     * 
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $manager, MailerService $mailer)
    {

        $this->denyAccessUnlessGranted('ROLE_USER');

        $presentation = new PPBase();

        $form = $this->createForm(
            CreatePresentationType::class,
            $presentation,
            array(

                // Time protection
                'antispam_time'     => true,
                'antispam_time_min' => 3,
                'antispam_time_max' => 3600,
            )
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $presentation->setCreator($this->getUser());

            $manager->persist($presentation);
            $manager->flush();

            $askForGuidance = $form->get('acceptGuidance')->getData();


            /* Email Webmaster that a new presentation has been created (moderation) */

            $sender = $this->getParameter('app.general_contact_email');
            $receiver = $sender;

            $emailParameters=[

                "goal" => $presentation->getGoal(),
                "address" => $this->generateUrl('show_presentation_by_id', ["id"=>$presentation->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
                
            ];

            $mailer->send($sender, 'Propon', $receiver, "A New Presentation Has Been Created",'/project_presentation/email_webmaster_notif_new_pp.html.twig', $emailParameters);

            return $this->redirectToRoute('presentation_helper', [
                "stringId" => $presentation->getStringId(),                
                "position" => 0,                
                "repeatInstance" => "false",                
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
     * @Route("/project-presentation/show-by-id/{id}/", name="show_presentation_by_id", priority=-2)
     * @Route("/projects/{stringId}/", name="long_path_show_presentation")
     * 
     * @return Response
     */
    public function show(PPBase $presentation, Request $request, TreatItem $specificTreatments, EntityManagerInterface $manager, CacheThumbnail $cacheThumbnail, ImageResizer $imageResizer, AssessQuality $assessQuality, UserPasswordHasherInterface $encoder, MailerService $mailer)
    {

        $this->denyAccessUnlessGranted('view', $presentation);

        $user = $this->getUser();

        $comment = new Comment;

        $comment->setUser($user)->setApproved(true);

        $commentForm = $this->createForm(CommentType::class, $comment,
        array(

            // Time protection
            'antispam_time'     => true,
            'antispam_time_min' => 4,
            'antispam_time_max' => 10000000,
        ));

        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {

            $presentation->addComment($comment);
            $manager->persist($comment);
            $manager->flush();

        }
            

        //updating views count only if user is not this presentation's presentor (as registered user or as a guest)

        if($user != $presentation->getCreator() && !array_key_exists('guest-presenter-token', $presentation->getData()) ){

            $presentation->setDataItem( 'viewsCount', $presentation->getDataItem('viewsCount') + 1 );

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

            $addNewsForm = $this->createForm(NewsType::class);
            $addNewsForm->handleRequest($request);

            if ($addNewsForm->isSubmitted() && $addNewsForm->isValid()) {
                
                dd("news form submitted and valid");
                $manager->flush();

                $this->addFlash(
                    'success fade-out',
                    "✅ Ajout effectué"
                );

                return $this->redirectToRoute(
                    'show_presentation',
    
                    [
    
                        'stringId' => $presentation->getStringId(),
                        '_fragment' => 'news-struct-container'
    
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

            $addDataListElemForm = $this->createForm(MiscDataType::class);
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

            $videoSlide = new Slide();
            $videoSlide->setType('youtube_video'); //only Youtube Videos are allowed currently.
            $addVideoForm = $this->createForm(VideoSlideType::class, $videoSlide);
            $addVideoForm->handleRequest($request);
            
            if ($addVideoForm->isSubmitted() && $addVideoForm->isValid()) {

                $youtubeVideoIdentifier = $specificTreatments->specificTreatments('youtube_video', $addVideoForm->get('address')->getData());//user might has given a complete youtube video url or just the video identifier. We extract the video identifier in the first case.

                $videoSlide->setAddress($youtubeVideoIdentifier);   

                // count previous slide in order to set new slides positions
                $videoSlide->setPosition(count($presentation->getSlides()));

                $presentation->addSlide($videoSlide);
                $manager->persist($videoSlide);

                $assessQuality->assessQuality($presentation);  

                $cacheThumbnail->cacheThumbnail($presentation);

                $this->addFlash(
                    'success',
                    "✅ Vidéo ajoutée"
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

                $imageResizer->edit($presentation, "logoFile");
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

            //create an account possibility for guest user presenters
            $guestPresenterForm = $this->createForm(WithoutUsernameRegistrationFormType::class);
            $guestPresenterForm->handleRequest($request);

            if ($guestPresenterForm->isSubmitted() && $guestPresenterForm->isValid()){


                $shadowUser = $presentation->getCreator(); // The fake user we created when we created an empty guest user presentation. We update this user.

                $guestPresenterEmail= $guestPresenterForm->get('email')->getData();

                // hashing guest user provided password
                
                $guestPresenterPassword = $encoder->hashPassword(
                    $shadowUser,
                    $guestPresenterForm->get('plainPassword')->getData()
                );


                $shadowUser ->setEmail($guestPresenterEmail)
                            ->setPassword($guestPresenterPassword);

                $presentation->setDataItem("guest-presenter-activated", true);

                //$presentation->unsetDataItem("guest-presenter-token"); //removed so that guest user can continue his work without being disconnected meanwhile.

                $manager->flush();

                $this->addFlash(
                    'success',
                    "✅ Votre présentation est enregistrée. Vous pouvez continuer à la modifier dès maintenant. Pour la modifier une autre fois, il suffira de vous connecter avec votre e-mail et votre mot de passe."
                );

                return $this->redirectToRoute('show_presentation', [
                    'stringId' => $presentation->getStringId(),
                ]);
            
            }

            return $this->render('/project_presentation/show.html.twig', [
                'presentation' => $presentation,
                'stringId' => $presentation->getStringId(),
                'contactUsPhone' => $this->getParameter('app.contact_phone'),
                'addWebsiteForm' => $addWebsiteForm->createView(),
                'addQAForm' => $addQAForm->createView(),
                'addECSForm' => $ecsForm->createView(),
                'addPersorgForm' => $addPersorgForm->createView(),
                'addDataListElemForm' => $addDataListElemForm->createView(),
                'addBusinessCardForm' => $addBusinessCardForm->createView(),
                'addDocumentForm' => $addDocumentForm->createView(),
                'addImageForm' => $addImageForm->createView(),
                'addVideoForm' => $addVideoForm->createView(),
                'addLogoForm' => $addLogoForm->createView(),
                'newUserForm' => $guestPresenterForm->createView(),
                'commentForm' => $commentForm->createView(),
                'addNewsForm' => $addNewsForm->createView(),
                
                
            ]);

        }

        // create a presentation form CTA 

         /* Create a Presentation Form */

         $newPresentation = new PPBase();

         $createPresentationFormCTA = $this->createForm(
             CreatePresentationType::class,
             $newPresentation,
             array(
 
                 // Time protection
                 'antispam_time'     => true,
                 'antispam_time_min' => 3,
                 'antispam_time_max' => 3600,
             )
         );
 
         $createPresentationFormCTA->handleRequest($request);
 
         if ($createPresentationFormCTA->isSubmitted() && $createPresentationFormCTA->isValid()) {
 
            $projectGoal = $createPresentationFormCTA->getData('goal');
             
            /* Email Webmaster that a new presentation has been created (moderation) */
 
             $sender = $this->getParameter('app.mailer_email');
             $receiver = $this->getParameter('app.general_contact_email');
 
             $emailParameters=[
 
                "goal" => $projectGoal,
                 
             ];
 
            $mailer->send($sender, 'Propon', $receiver, "A New Presentation Has Been Created",'Project Goal : '.$projectGoal);
 
            return $this->redirectToRoute('edit_presentation_as_guest_user', [
                'goal' => $projectGoal,
            ]);
 
        }

        return $this->render('/project_presentation/show.html.twig', [
            'presentation' => $presentation,
            'stringId' => $presentation->getStringId(),
            'contactUsPhone' => $this->getParameter('app.contact_phone'),
            'createPresentationFormCTA' => $createPresentationFormCTA->createView(),
            'commentForm' => $commentForm->createView(),
            
        ]);

    }



    /**
     * Allow anonymous user to create a presentation and then create an account in order to save it.
     * Landing is on which page the user will arrive (presentation helper, or wysiwyg presentation page)
     * 
     * @Route("/presenter-un-projet/{goal}", name="edit_presentation_as_guest_user")
     * 
     * @return Response
    */
    public function guestUserEditPresentation(RequestStack $requestStack, EntityManagerInterface $manager, SluggerInterface $slugger, $goal){

        //Creating a php session token attached to anonymous user
        //This token is also attached to the newly created presentation
        //So that we can check and distinguish if guest user can edit a presentation.

        $guestPresenterToken = substr(str_shuffle(MD5(microtime())), 0, 15);
        $session = $requestStack->getSession();
        $session->set('guest-presenter-token', $guestPresenterToken);


        // Creation of an "empty shadow user" for the purpose of editing a presentation as a guest user

        $newUser= new User();
        $anonymousUserNameId = substr(str_shuffle(MD5(microtime())), 0, 6); // creating a random username for shadow user

        $newUser->setUserName('user'.$anonymousUserNameId)
                ->setEmail('test'.$anonymousUserNameId.'@test.com')
                ->setUserNameSlug(strtolower($slugger->slug($newUser->getUserName())))
                ->setPassword('test'.$anonymousUserNameId)
                ->setParameter('isVerified', true);

                $manager->persist($newUser);

        // Creation of an "empty" project presentation which can be edited by the guest user
        $presentation = new PPBase();

        $presentation->setDataItem('guest-presenter-token', $guestPresenterToken)
                    ->setDataItem("guest-presenter-activated", false)
                    ->setGoal($goal)
                    ->setCreator($newUser);
        
        $manager->persist($presentation);

        $manager->flush();

        return $this->redirectToRoute('presentation_helper', [
            'stringId' => $presentation->getStringId(),
            'position' => 0,
            'repeatInstance' => "false",
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

            session_write_close();

            $data = json_decode($request->request->get('data'), true);

            $presentation->setDataItem($data['key'], $data['value']);

            $manager->flush();
          
            return  new JsonResponse(true);

        }

        return  new JsonResponse();

    }
        
  
    /** 
     * Allow to live save some presentation settings
     * 
     * @Route("/projects/{stringId}/ajax-set-data-legacy", name="ajax_presentation_settings_legacy") 
     * 
    */ 

    public function ajaxSetDataLegacy(Request $request, PPBase $presentation,EntityManagerInterface $manager, MailerService $mailer) {

        $this->denyAccessUnlessGranted('edit', $presentation);

        if ($request->isXmlHttpRequest()) {

            session_write_close();
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

                case 'admin-validation-presentation-switch':

                    if ($this->isGranted('ROLE_ADMIN')) {

                        $presentation->setIsAdminValidated($switchState);

                        if ($switchState=="true") {            
                            
                            /* Email user presenter its presentation has been validated */

                            $sender = $this->getParameter('app.general_contact_email');
                            
                            $receiver = $presentation->getCreator()->getEmail();

                            $emailParameters=[

                                "address" => $this->generateUrl('show_presentation', ["stringId"=>$presentation->getStringId()], UrlGeneratorInterface::ABSOLUTE_URL),
                                
                            ];

                            $mailer->send($sender, 'Propon', $receiver, "Votre présentation de projet est validée sur Propon.",'/project_presentation/email_presenter_validated_pp.html.twig', $emailParameters);

                        }
    
                    }

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
     * Allow to inline live save some presentation elements
     * 
     * @Route("/project/ajax-inline-save", name="live_save_pp") 
     * 
    */ 
    public function ajaxPPLiveSave(LiveSavePP $liveSave, Request $request) {
        
        if ($request->isXmlHttpRequest()) {
            
            session_write_close();

            /* Getting posted data */

            $metadata = json_decode($request->request->get('metadata'), true);

            $entityName = ucfirst($metadata['entity']); //ex : "PPBase"; "Slide"
            $entityId = $metadata['id']; //ex: 2084
            $property = $metadata['property']; //ex : "websites" (websites is a key from the $otherComponents attribute from PPBase entity)

            $subId = isset($metadata["subid"]) ? $metadata["subid"] : null ; //ex: a website id
            $subProperty = isset($metadata["subproperty"]) ? $metadata["subproperty"] : null ; //ex : "url" (url is a key from above mentionned websites array)
            
            $content = trim($request->request->get('content'));

            $liveSave->hydrate($entityName, $entityId, $property, $subId, $subProperty, $content);

            if( ! $liveSave->allowUserAccess() ){

                return new JsonResponse(
                
                    [],
                    Response::HTTP_FORBIDDEN,
                );

            }

            if( ! $liveSave->allowItemAccess() ){

                return new JsonResponse(
                
                    [],
                    Response::HTTP_BAD_REQUEST,
                );

            }

            $validateContent = $liveSave->validateContent();

            if( is_string($validateContent) ){

                return new JsonResponse(
                
                    [
                        'error' =>  $validateContent,
                    ],

                    Response::HTTP_BAD_REQUEST,
                );

            }

            $liveSave->save();

            return  new JsonResponse(true);

        }

        return  new JsonResponse();

    }

    /**
     * Allow to like - unlike a presentation
     * 
     * @Route("/project/{stringId}/like", name="ajax_like_pp")
     *
     */
    public function ajaxLike(PPBase $presentation, EntityManagerInterface $manager, LikeRepository $likeRepo)
    {
        $user = $this->getUSer();

        if(!$user){

            return new JsonResponse(
                
                [],
                Response::HTTP_FORBIDDEN,
            );

        }

        if($presentation->isLikedByUser($user)){

            $like = $likeRepo->findOneBy(

                [
                    "projectPresentation" => $presentation,
                    "user" => $user,
                ]

            );

            $manager->remove($like);
            $manager->flush();

            return new JsonResponse(
                
                [
                    "code" => 200,
                    "action" => "remove",
                    "likesCount" => $likeRepo->count(["projectPresentation"=>$presentation]),

                ],
            );
        
        } else {

            $like = new Like();

            $like   -> setUser($user)
                    -> setProjectPresentation($presentation);

            $manager->persist($like);
            $manager->flush();

            return new JsonResponse(
                
                [
                    "code" => 200,
                    "action" => "add",
                    "likesCount" => $likeRepo->count(["projectPresentation"=>$presentation]),

                ],
            );
                
        };

        return  new JsonResponse(false);


    }

    /**
     * Allow to edit pp stringId
     * 
     * @Route("/projects/{stringId}/validate-string-id", name="validate_pp_string_id")
     * 
     * @return void
     */
    public function validateStringId(PPBase $presentation, EntityManagerInterface $manager)
    {

        $presentation->setDataItem("validatedStringId", true);
        $manager->flush();

        $presentationURL = $this->generateUrl('show_presentation', [
            'stringId' => $presentation->getStringId(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $this->addFlash(
            'success',
            "👉 L'adresse de votre page de projet est <b>$presentationURL</b><br>
            👉 Pour la copier, partager, ou modifier, utilisez le bouton \"Partager la présentation\" en bas de page."
        );

        return $this->redirectToRoute('show_presentation', [
            'stringId' => $presentation->getStringId(),
            '_fragment' => 'flash-messages'
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

        return $this->render('project_presentation/edit/title_goal_logo/_update_string_id_form.html.twig', [
            'presentation' => $presentation,
            'stringId' => $presentation->getStringId(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * Allow to edit pp thumbnail
     * 
     * @Route("/projects/{stringId}/edit/thumbnail", name="edit_pp_thumbnail")
     * 
     * @return void
     */
    public function editThumbnail(PPBase $presentation, Request $request, EntityManagerInterface $manager, ImageResizer $imageResizer, CacheThumbnail $cacheThumbnail)
    {

        $this->denyAccessUnlessGranted('edit', $presentation);

        $form = $this->createForm(PPBaseType::class, $presentation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
                             
            $manager->flush();

            $cacheThumbnail->cacheThumbnail($presentation);

            $this->addFlash(
                'success',
                "✅ La vignette de votre présentation est modifiée !"
            );

            return $this->redirectToRoute('show_presentation', [

                'stringId' => $presentation->getStringId(),
            ]);

        }

        return $this->render('project_presentation/edit/edit_custom_thumbnail.html.twig', [
            'presentation' => $presentation,
            'stringId' => $presentation->getStringId(),
            'form' => $form->createView(),

        ]);

    }


    /**
     * An admin can create a project presentation for someone else, and then creates an account for this person or organisation, and automaticaly transfer presentation to this new account (so that new account can improve it by himself).
     * 
     * @Route("/admin/transfer-presentation/{stringId}", name="transfer_presentation")
    */
    public function transferPresentation(PPBase $presentation, Request $request, UserPasswordHasherInterface $passwordHasher, SluggerInterface $slugger): Response
    {

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Presentation should be admin validated before being transfered to a new user (otherwise this recipient new user won't be able to see his presentation accessible by visitors on the website)

        if(!$presentation->getIsAdminValidated()){

            $this->addFlash('danger', "!!! La présentation n'a pas encore été validée par un administrateur du site. Demandez-lui de la valider avant qu'elle puisse être transférée à un autre compte.");

            return $this->redirectToRoute('show_presentation', [
                'stringId' => $presentation->getStringId(),
            ]);

        }

        // Creating an new user in order to transfer presentation to him

        $newUser = new User();

        $form = 
        
            $this->createForm(

                RegistrationFormType::class, 
                $newUser,
            );


        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

             // encode the plain password
             $newUser->setPassword(

                $passwordHasher->hashPassword($newUser, $form->get('plainPassword')->getData())
            );

                
            // creating a unique username slug
            $slug = strtolower($slugger->slug($newUser->getUserName()));

            $slugs = $this->getDoctrine()->getRepository(User::class)->createQueryBuilder('u')->where('u.userNameSlug LIKE :slug')->setParameter('slug', $slug.'%')->getQuery()->getResult();

            if (! empty($slugs)) {

                $slug .= '-' . count($slugs); //this method does not work if user rows can be deleted + it does not provide reliable increment (ex : 1) My post -> my-post 2) My -> my-1 (instead of my))

            }    

            $newUser->setUserNameSlug($slug);  

            // creating an user's public profile

            $persorg = new Persorg();
            $persorg->setName($newUser->getUserName());
            $newUser->setPersorg($persorg);

            // save new user in database
            try {

                $newUser->setParameter('isVerified', true);
                $newUser->setEmailValidationToken(null);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($newUser);
                $entityManager->flush();

            } catch (\Exception $e) {

                $this->addFlash('warning', $e->getMessage());

                return $this->redirectToRoute('show_presentation', [
                    'stringId' => $presentation->getStringId(),
                ]);
                
            }

            // transfering presentation edition rights to new user
            try {

                $presentation->setCreator($newUser);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($presentation);
                $entityManager->flush();

            } catch (\Exception $e) {

                $this->addFlash('warning', $e->getMessage());

                return $this->redirectToRoute('show_presentation', [
                    'stringId' => $presentation->getStringId(),
                ]);

            }


            $this->addFlash(
                'success',
                "✅ Le nouveau compte a été créé et la présentation a été transférée à ce nouveau compte."
            );

            return $this->redirectToRoute('show_presentation', [
                'stringId' => $presentation->getStringId(),
            ]);
        }

        return $this->render('/project_presentation/admin_transfers_presentation_to_new_account.html.twig', [

            'form' => $form->createView(),
            'stringId' => $presentation->getStringId(),
            
        ]);

    }



}

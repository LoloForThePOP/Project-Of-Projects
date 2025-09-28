<?php

namespace App\Controller;

use App\Entity\Need;
use App\Entity\Slide;
use App\Service\Slug;
use App\Entity\PPBase;
use App\Service\TreatItem;
use App\Service\ImageResizer;
use App\Service\AssessQuality;
use App\Service\CacheThumbnail;
use App\Service\CreatePPService;
use App\Form\PresentationHelperType;
use App\Repository\PPBaseRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PresentationHelperController extends AbstractController
{

    /**
     * Allow user to follow a step by step guidance to present her project
     * 
     * position = 0 means first step; position = null means last step.
     * 
     * @Route("step-by-step-project-presentation/{stringId?}/{position?0}/{repeatInstance?}", requirements={"position"="\d+"}, name="presentation_helper")
     */
    public function origin($stringId=null, $position = 0, $repeatInstance = "false", Request $request, EntityManagerInterface $manager,  TreatItem $specificTreatments, Slug $slug, CacheThumbnail $cacheThumbnail, ImageResizer $imageResizer, AssessQuality $assessQuality): Response
    {

        // TO DO : ask ai to choose categories and keywordsfor user.
        // add a keyword field maybe anyway

        //$this->denyAccessUnlessGranted('edit', $presentation);



        
        $request->attributes->set('googleMapApiKey', $this->getParameter('app.google_map_api_key'));

        if($stringId==null){

            $presentation = new PPBase();

        }else{

            $presentation = $this->getDoctrine()->getRepository(PPBase::class)->findOneBy(['stringId' => $stringId]);

        }

        $form = $this->createForm(
            PresentationHelperType::class, $presentation,
            array(

                // Time protection
                'antispam_time'     => true,
                'antispam_time_min' => 3,
                'antispam_time_max' => 3600,
            )
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if($stringId==null){

                $goal = $form->get('goal')->getData();

                $presentation->setGoal($goal);
                $presentation->setCreator($this->getUser());

                $manager->persist($presentation);
                $manager->flush();

                return $this->redirectToRoute('presentation_helper', [

                    'stringId' => $presentation->getStringId(),
                    'position' => 1,
                    'repeatInstance' => $repeatInstance,
                                
                ]);
             
            }

            else{


                if($position==8){

                    $assessQuality->assessQuality($presentation);

                    $this->addFlash(
                        'success fs-4',
                        "✅ Votre page de présentation est prête. Apportez-lui toutes les modifications que vous désirez. <br> 🙋 Si vous avez besoin d'aide, utilisez le bouton d'aide rapide en bas de page."
                    );

                    return $this->redirectToRoute('show_presentation', [

                        'stringId' => $presentation->getStringId(),
                                    
                    ]);

                }

                $helperType=$form->get('helperItemType')->getData();


                if ($helperType=="title") {

                    $title = $form->get('title')->getData();

                    if(!empty($title)){

                        $presentation->setTitle($title);

                        //setting a slug
                        $stringId = $slug->slugInput($title);

                        // checking if result is unique
                        $twin = $this->getDoctrine()->getRepository(PPBase::class)->findOneBy(['stringId' => $stringId]);

                        if ($twin !== null) {

                            $stringId .="-".$presentation->getId();
                            
                        }

                        $presentation->setStringId($stringId);

                        $manager->flush();

                    }

                }

                if ($helperType=="logo") {

                    $logo = $form->get('logoFile')->getData();

                    if (isset($logo) && $logo !== '') {
                        
                        $presentation->setLogoFile($logo);

                        $manager->flush();

                        $imageResizer->edit($presentation, 'logoFile');
                        $cacheThumbnail->cacheThumbnail($presentation);
                    }
                   

                }

                if ($helperType=="imageSlide") {
                    
                    $slide = $form->get('imageSlide')->getData();

                    if ($slide->getId() != null) {
                       
                        $slide = new Slide();

                        $slide->setType('image');

                        $manager->persist($slide);
                        $presentation->addSlide($slide);

                        $manager->flush();

                        $imageResizer->edit($slide);
                        $cacheThumbnail->cacheThumbnail($presentation);  

                    }



                    

                }

                if ($helperType=="websites") {


                    $url = $form->get('url')->getData();


                    if (filter_var($url, FILTER_VALIDATE_URL)){

                        $websiteItem = 
                        
                            [
                                "description" => $form->get('websiteDescription')->getData(),
                                "url" =>$url,
                            ]
                        ;

                        $websiteItem = $specificTreatments->specificTreatments('websites', $websiteItem);

                        $presentation->addOtherComponentItem('websites', $websiteItem);

                        $manager->flush();

                    }


                }

                if ($helperType=="needs") {


                    $needTitle = $form->get('needTitle')->getData();
                    $needDescription = $form->get('needDescription')->getData();
                    $needType =  $form->get('selectedNeedType')->getData();

                    if (isset($needTitle) && $needTitle !== ''  && isset($needDescription) && $needDescription !== '') {

                        $need = new Need();

                        $need->setTitle($needTitle);
                        $need->setDescription($needDescription);
                        $need->setType($needType);

                        $presentation->addNeed($need);

                        $manager->persist($need);
                        $manager->flush();

                    }


                    
                }

                if ($helperType=="textDescription") {

                    $string = "<p>".nl2br($answer=$form->get('answer')->getData())."</p>";

                    $presentation->setTextDescription($presentation->getTextDescription().$string);

                    $manager->flush();

                }

                if ($helperType=="qa") {

                    $question=$form->get('finalRenderingLabel')->getData();
                    $answer=$form->get('answer')->getData();

                    $qaItem = 
                    
                        [
                            "question" => $question,
                            "answer" => $answer,
                        ]
                    ;

                    $presentation->addOtherComponentItem('questionsAnswers', $qaItem);
                    $manager->flush();

                }

            

                $currentPosition=$form->get('currentPosition')->getData();

                $nextPosition=$form->get('nextPosition')->getData();

                // Do we repeat current position (example : user wants to add another website)

                $repeatInstance = $form->get('repeatedInstance')->getData(); // set to false by default in form type definition.

                if ($repeatInstance == "true") {
                    $nextPosition = $currentPosition;
                }

                return $this->redirectToRoute('presentation_helper', [

                    'stringId' => $presentation->getStringId(),
                    'presentation' => $presentation,
                    'position' => $nextPosition,
                    'repeatInstance' => $repeatInstance,
                                
                ]);

            }

        }

        return $this->render('presentation_helper/origin.html.twig', [
            'form' => $form->createView(),
            'position' => $position,
            'stringId' => 't3a99o',
            /* 'presentation' => $presentation, */
            'repeatInstance' => $repeatInstance,
        ]);

    }
    

}

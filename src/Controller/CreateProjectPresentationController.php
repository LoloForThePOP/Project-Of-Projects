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

class CreateProjectPresentationController extends AbstractController
{

    /**
     * Allow user to follow a step by step form to present their project
     * 
     * position = 0 means first step; position = null means last step.
     * 
     * @Route("step-by-step-project-presentation/{position?0}/{stringId?}/{repeatInstance}", name="project_presentation_helper", defaults={"repeatInstance": "false"})
     */
    public function origin($stringId = null, $position = 0, $repeatInstance = "false", Request $request, EntityManagerInterface $manager,  Slug $slug, CacheThumbnail $cacheThumbnail, ImageResizer $imageResizer, AssessQuality $assessQuality): Response
    {

        // TO DO : ask ai to choose categories and keywordsfor user.
        // maybe add a keyword field anyway (or let ai manage...)

        if($stringId == null){

            $this->denyAccessUnlessGranted('ROLE_USER');

            $presentation = new PPBase();

        }else{

            $presentation = $this->getDoctrine()->getRepository(PPBase::class)->findOneBy(['stringId' => $stringId]);

            $this->denyAccessUnlessGranted('edit', $presentation);

        }

        $form = $this->createForm(
            PresentationHelperType::class, $presentation,

        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Case user types project goal for the first time: we create a presentation in DB

            if($stringId==null){

                $goal = $form->get('goal')->getData();

                $presentation->setGoal($goal);
                $presentation->setCreator($this->getUser());

                $manager->persist($presentation);
                $manager->flush();

                return $this->redirectToRoute('project_presentation_helper', [

                    'stringId' => $presentation->getStringId(),
                    'position' => 1,
                    'repeatInstance' => $repeatInstance,
                                
                ]);
             
            }

            // Case user has already defined a project goal: project presentation exists in DB

            else{

                $nextPosition=$form->get('nextPosition')->getData();

                // Case user has finished project step by step presentation

                if($nextPosition==null){


                    // TO DO: 
                    // Put a flag in PPBase object so that we know that user engaged until the end so that we can clean db with junk presentations.
                    // Send an email to admin so that they can validate presentation or not

                    $assessQuality->assessQuality($presentation);

                    $this->addFlash(
                        'success fs-4',
                        "✅ Votre page de présentation est prête. Apportez-lui toutes les modifications que vous désirez. <br> 🙋 Si vous avez besoin d'aide, utilisez le bouton d'aide rapide en bas de page."
                    );

                    return $this->redirectToRoute('show_presentation', [

                        'stringId' => $presentation->getStringId(),
                                    
                    ]);

                }

                // Case user has not finished step by step presentation

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



                if ($helperType=="textDescription") {

                    $string = nl2br($form->get('textDescription')->getData());

                    $presentation->setTextDescription($string);

                    $manager->flush();

                }

                           


                return $this->redirectToRoute('project_presentation_helper', [

                    'stringId' => $presentation->getStringId(),
                    'presentation' => $presentation,
                    'position' => $nextPosition,
                                
                ]);

            }

        }

        return $this->render('project_presentation/create/origin.html.twig', [
            'form' => $form->createView(),
            'stringId' => $stringId,
            'position' => $position,
        ]);

    }
    

}

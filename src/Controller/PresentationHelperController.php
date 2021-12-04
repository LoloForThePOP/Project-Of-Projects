<?php

namespace App\Controller;

use App\Entity\Need;
use App\Entity\PPBase;
use App\Service\TreatItem;
use App\Form\PresentationHelperType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PresentationHelperController extends AbstractController
{
    
    
    /**
     * Allow user to follow a step by step guide to present its project
     * 
     * position = 0 means begining; position = null means end.
     * 
     * @Route("{stringId}/helper/{position}/{repeatInstance}", requirements={"position"="\d+"}, name="presentation_helper")
     */
    public function origin(PPBase $presentation, Request $request, EntityManagerInterface $manager, $position = null, $repeatInstance='false', TreatItem $specificTreatments, CategoryRepository $categoryRepository): Response
    {

        $this->denyAccessUnlessGranted('edit', $presentation);

        $categories = $categoryRepository->findBy([], ['position' => 'ASC']);

       

        if($position==null){

            $this->addFlash(
                'success fs-4',
                "✅ Votre page de présentation est prête. Apportez-lui toutes les modifications que vous désirez. <br> 🙋 Si vous avez besoin d'aide, utilisez le bouton d'aide rapide en bas de page."
            );

            return $this->redirectToRoute('show_presentation', [

                'stringId' => $presentation->getStringId(),
                               
            ]);

        }
        
        $request->attributes->set('googleMapApiKey', $this->getParameter('app.google_map_api_key'));

        $form = $this->createForm(
            PresentationHelperType::class,null,
            array(

                // Time protection
                'antispam_time'     => true,
                'antispam_time_min' => 4,
                'antispam_time_max' => 3600,
            )
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $helperType=$form->get('helperItemType')->getData();

            if ($helperType=="title") {

                $title=$form->get('title')->getData();
                $presentation->setTitle($title);

                $manager->flush();

            }

            if ($helperType=="websites") {

                $websiteDescription=$form->get('websiteDescription')->getData();
                $websiteURL=$form->get('url')->getData();

                $websiteItem = 
                
                    [
                        "description" => $websiteDescription,
                        "url" => $websiteURL,
                    ]
                ;

                $websiteItem = $specificTreatments->specificTreatments('websites', $websiteItem);

                $presentation->addOtherComponentItem('websites', $websiteItem);
                $manager->flush();

            }

            if ($helperType=="needs") {

                $need = new Need();

                $need->setTitle($form->get('needTitle')->getData());
                $need->setDescription($form->get('needDescription')->getData());
                $need->setType($form->get('selectedNeedType')->getData());

                $presentation->addNeed($need);

                $manager->persist($need);
                $manager->flush();
                
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

            // Do we repeat (example : user wants to add another website)

            $repeatInstance = $form->get('repeatedInstance')->getData(); // set to false by default in form type definition.

            if ($repeatInstance == "true") {
                $nextPosition = $currentPosition;
            }

            if ($nextPosition != null) {

                $this->addFlash(
                    'success fs-4',
                    "✅ La construction de votre page de projet progresse."
                );
                
            }

            return $this->redirectToRoute('presentation_helper', [

                'stringId' => $presentation->getStringId(),
                'presentation' => $presentation,
                'categories' => $categories,
                'position' => $nextPosition,
                'repeatInstance' => $repeatInstance,
                               
            ]);

        }

        return $this->render('presentation_helper/origin.html.twig', [
            'form' => $form->createView(),
            'position' => $position,
            'categories' => $categories,
            'stringId' => $presentation->getStringId(),
            'presentation' => $presentation,
            'repeatInstance' => $repeatInstance,
        ]);

    }
    

}

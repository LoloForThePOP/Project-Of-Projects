<?php

namespace App\Controller;

use App\Entity\PPBase;
use App\Service\TreatItem;
use App\Form\PresentationHelperType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Need;

class PresentationHelperController extends AbstractController
{
    
    
    /**
     * Allow user to follow a step by step guide to present its project
     * 
     * position = 0 means begining; position = null means end.
     * 
     * @Route("{stringId}/helper/{position}/{repeatInstance}", requirements={"position"="\d+"}, name="presentation_helper")
     */
    public function origin(PPBase $presentation, Request $request, EntityManagerInterface $manager, $position = null, $repeatInstance='false', TreatItem $specificTreatments): Response
    {

        $this->denyAccessUnlessGranted('edit', $presentation);

        if($position==null){

            $this->addFlash(
                'success fs-4',
                "✅ Votre page de présentation est prête. Apportez-lui toutes les modifications que vous désirez."
            );

            return $this->redirectToRoute('show_presentation', [

                'stringId' => $presentation->getStringId(),
                               
            ]);

        }

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
                
                //dd($need);

            }

            if ($helperType=="textDescription") {

                $string = "<p><strong>".$question=$form->get('questionAsked')->getData()."</strong><br><br>".nl2br($answer=$form->get('answer')->getData())."</p>";

                $presentation->setTextDescription($presentation->getTextDescription().$string);

                //$manager->flush();

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

            //dd($nextPosition);

            // Do we repeat (example : user wants to add another website)

            $repeatInstance = $form->get('repeatedInstance')->getData(); // set to false by default in form type definition.

            if ($repeatInstance == "true") {
                $nextPosition = $currentPosition;
            }

            //dd($nextPosition);

            $this->addFlash(
                'success fs-4',
                "✅ Votre page de projet a été mise à jour !"
            );

            $request->attributes->set('mykey', 'myvalue');
            
            return $this->redirectToRoute('presentation_helper', [

                'stringId' => $presentation->getStringId(),
                'position' => $nextPosition,
                'repeatInstance' => $repeatInstance,

                'googleMapApiKey' => $this->getParameter('app.google_map_api_key'),
                               
            ]);

        }

        return $this->render('presentation_helper/origin.html.twig', [
            'form' => $form->createView(),
            'position' => $position,
            'stringId' => $presentation->getStringId(),
            'repeatInstance' => $repeatInstance,

            'googleMapApiKey' => $this->getParameter('app.google_map_api_key'),
        ]);

    }
    

}

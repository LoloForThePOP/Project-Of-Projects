<?php

namespace App\Controller;

use App\Entity\PPBase;
use App\Form\PresentationHelperType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PresentationHelperController extends AbstractController
{
    
    
    /**
     * @Route("{stringId}/helper/{position}", requirements={"position"="\d+"}, name="presentation_helper")
     */
    public function origin(PPBase $presentation, Request $request, EntityManagerInterface $manager, $position=0): Response
    {

        // ???to keep $this->denyAccessUnlessGranted('ROLE_USER');

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

            //dd($form);

        /*  $presentation->setCreator($this->getUser());

            $manager->persist($presentation);
            $manager->flush();
        */
            $this->addFlash(
                'success fs-4',
                "✅ slkfjslkdjfslkdfjslkdjf sldkf slkdj f."
            );

            return $this->redirectToRoute('presentation_helper', [
                
                             
            ]);
        }

        return $this->render('presentation_helper/origin.html.twig', [
            'QA_Form' => $form->createView(),
            'position' => $position,
        ]);

    }
    

}

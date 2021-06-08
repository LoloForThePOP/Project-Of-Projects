<?php

namespace App\Controller;

use App\Entity\PPBase;
use App\Form\CreatePresentationType;
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
     * @Route("/projects/create",name="create_presentation")
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
                'antispam_time_min' => 8,
                'antispam_time_max' => 3600,
            )
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $presentation->setCreator($this->getUser());

            $manager->persist($presentation);
            $manager->flush();

            $manager->persist($presentation);
            $manager->flush();

            $this->addFlash(
                'success',
                'La présentation du projet a été créée ! <br> Vous pouvez maintenant ajouter les informations que vous désirez présenter.'
            );

            return $this->redirectToRoute('homepage', []);
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
    public function show(PPBase $presentation)
    {

        $user = $this->getUser();

        $this->denyAccessUnlessGranted('view', $presentation);

        return $this->render('/project_presentation/show.html.twig', [
            'presentation' => $presentation,
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\PPBase;
use App\Service\UluleAPI;
use App\Service\MailerService;
use App\Form\CreatePresentationType;
use Algolia\SearchBundle\SearchService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{

    
    /**
     * @Route("/", name="homepage")
     */
    public function index(Request $request, EntityManagerInterface $manager, MailerService $mailer): Response
    {

        /* Create a Presentation Form */

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

            $projectGoal = $form->getData('goal');
            
            /* Email Webmaster that a new presentation has been created (moderation) */

            $sender = $this->getParameter('app.mailer_email');
            $receiver = $this->getParameter('app.general_contact_email');

            $emailParameters=[

                "goal" => $projectGoal,
                
            ];

            $mailer->send($sender, 'Propon', $receiver, "A New Presentation Has Been Created",'Project Goal : '.$projectGoal);


            if ($this->isGranted('ROLE_USER')) {

                $presentation->setCreator($this->getUser());

                $manager->persist($presentation);
                $manager->flush();

                return $this->redirectToRoute('presentation_helper', [
                    "stringId" => $presentation->getStringId(),                
                    "position" => 0,                
                    "repeatInstance" => "false",                
                ]);

            }

            else{

                return $this->redirectToRoute('edit_presentation_as_guest_user', [
                    'goal' => $projectGoal,
                ]);
            }

        }

        $presentations = $manager->createQuery('SELECT p FROM App\Entity\PPBase p WHERE p.isPublished=true AND p.overallQualityAssessment>=2 AND p.isAdminValidated=true AND p.isDeleted=false ORDER BY p.createdAt DESC')->getResult();
     
        // last 20 inserted projects presentations

        $lastInsertedPresentations = array_slice($presentations, 0, 29);

        $presentationsCount= count($presentations);

        // Some Hilighted Projects at random

        shuffle($lastInsertedPresentations);
        $highlightedProjects = array_slice($lastInsertedPresentations, 0, 5);

        
        /* UluleAPI $ulule,
        $ulule->fetchProjectInfo(); just testing Ulule api*/

        return $this->render("/home/homepage.html.twig", [
            'lastInsertedPresentations' => $lastInsertedPresentations,
            'highlightedProjects' => $highlightedProjects,
            'form' => $form,
        ]);

    }

    

}

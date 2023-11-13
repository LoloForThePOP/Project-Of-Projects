<?php

namespace App\Controller;

use App\Entity\News;
use App\Entity\PPBase;
use App\Form\NewsType;
use App\Service\UluleAPI;
use App\Service\MailerService;
use App\Form\CreatePresentationType;
use Algolia\SearchBundle\SearchService;
use App\Repository\ArticleRepository;
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
    public function index(Request $request, EntityManagerInterface $manager, MailerService $mailer, ArticleRepository $artRepo): Response
    {

        /* Create a Presentation Form */

        $presentation = new PPBase();

        $setGoalForm = $this->createForm(
            CreatePresentationType::class,
            $presentation,
            array(

                // Time protection
                'antispam_time'     => true,
                'antispam_time_min' => 3,
                'antispam_time_max' => 3600,
            )
        );

        $setGoalForm->handleRequest($request);

        if ($setGoalForm->isSubmitted() && $setGoalForm->isValid()) {

            $projectGoal = $setGoalForm->getData('goal');
            
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


        if ($this->isGranted('ROLE_USER')) {

            $news = new News();
            $addNewsForm = $this->createForm(NewsType::class, $news);
            $addNewsForm->handleRequest($request);

            if ($addNewsForm->isSubmitted() && $addNewsForm->isValid()) {

                //Searching for Targeted Presentation (user can have several presented projects, so we got to know which project is targeted by the news):

                //$targetedPresentationId = $addNewsForm->get('presentationId')->getData();
                //$targetedPresentation = $this->entityManager->getRepository(News::class)->findOneById($targetedPresentationId);
                //$news->setProject($targetedPresentation);



                $news->setAuthor($this->getUser());
                $manager->persist($news);
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


        }


        $presentations = $manager->createQuery('SELECT p FROM App\Entity\PPBase p WHERE p.isPublished=true AND p.overallQualityAssessment>=2 AND p.isAdminValidated=true AND p.isDeleted=false ORDER BY p.createdAt DESC')->getResult();
     
        // last 20 inserted projects presentations

        $lastInsertedPresentations = array_slice($presentations, 0, 29);

        $presentationsCount= count($presentations);

        // Some Hilighted Projects at random

        shuffle($lastInsertedPresentations);
        $highlightedProjects = array_slice($lastInsertedPresentations, 0, 5);


        $articles = array_reverse($artRepo->findAll());

        if ($this->isGranted('ROLE_USER')) { // we add news form to connected users

            return $this->render("/home/homepage.html.twig", [
                'lastInsertedPresentations' => $lastInsertedPresentations,
                'highlightedProjects' => $highlightedProjects,
                'setGoalForm' => $setGoalForm->createView(),
                'addNewsForm' => $addNewsForm->createView(),
                'articles' => $articles,
            ]);

        }

        return $this->render("/home/homepage.html.twig", [
            'lastInsertedPresentations' => $lastInsertedPresentations,
            'highlightedProjects' => $highlightedProjects,
            'setGoalForm' => $setGoalForm->createView(),
            'articles' => $articles,
        ]);

    }

    

}

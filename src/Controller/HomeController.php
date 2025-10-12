<?php

namespace App\Controller;

use App\Entity\News;
use App\Entity\PPBase;

use App\Form\NewsType;
use App\Form\CreatePresentationType;

use App\Repository\ArticleRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


// Homepage route
class HomeController extends AbstractController
{
    
    /**
     * @Route("/", name="homepage")
     */
    public function index(Request $request, EntityManagerInterface $manager, ArticleRepository $artRepo): Response
    {

        /* Create a presentation form directly in homepage */

        $presentation = new PPBase();

        $setGoalForm = $this->createForm(
            CreatePresentationType::class,
            $presentation);

        $setGoalForm->handleRequest($request);

        // create a presentation form submission handling

        if ($setGoalForm->isSubmitted() && $setGoalForm->isValid()) {
           
            $projectGoal = $setGoalForm->getData()->getGoal();

            $this->get('session')->set('project_goal', $projectGoal); //Setting goal in a session to reuse it in the route bellow

            return $this->redirectToRoute('ai_interview_helper_origin', []); //entering AI project presentation helper so that user can continue its presentation in an appropriate interface.

        }

        //If user is logged in 

        if ($this->isGranted('ROLE_USER')) {

            //User might have presented some projects (to do: we should test it before)
            //News form if user wants to directly publish news about a project

            $news = new News();
            $addNewsForm = $this->createForm(NewsType::class, $news);
            $addNewsForm->handleRequest($request);

            //User is submitting a news
            if ($addNewsForm->isSubmitted() && $addNewsForm->isValid()) {

                //Getting which user's project id is targeted by the news 
                $targetedPresentationId = $addNewsForm->get('presentationId')->getData();

                //Actually getting the presentation in db
                $targetedPresentation = $this->getDoctrine()->getManager()->getRepository(PPBase::class)->findOneBy(['id' => $targetedPresentationId]);

                //Making sure user has rights to publish news about this presentation
                if ($targetedPresentation->getCreator() !=$this->getUser()) {
                    throw new \Exception("Not Allowed :-)");
                }

                //Hydrating news object and storing it in db

                $news->setProject($targetedPresentation);
                $news->setAuthor($this->getUser());

                $manager->persist($news);
                $manager->flush();

                $this->addFlash(
                    'success fade-out',
                    "✅ Ajout effectué"
                );

                //Redirecting user to project presentation he has just published a news about

                return $this->redirectToRoute(
                    'show_presentation',
    
                    [
    
                        'stringId' => $targetedPresentation->getStringId(),
                        '_fragment' => 'news-struct-container'
    
                    ]

                );

            }


        }

        //Getting last inserted presentation to display on homepage

        $presentations = $manager->createQuery('SELECT p FROM App\Entity\PPBase p WHERE p.isPublished=true AND p.overallQualityAssessment>=2 AND p.isAdminValidated=true AND p.isDeleted=false ORDER BY p.createdAt DESC')->getResult();
     
        //Selecting last 20 inserted projects presentations

        $lastInsertedPresentations = array_slice($presentations, 0, 29);

        //Getting some articles to display on Homepage

        $articles = array_reverse($artRepo->findAll());

        //returning a view for logged in users (we add the create a news form)

        if ($this->isGranted('ROLE_USER')) { 

            return $this->render("/home/homepage.html.twig", [
                'lastInsertedPresentations' => $lastInsertedPresentations,
                'setGoalForm' => $setGoalForm->createView(),
                'addNewsForm' => $addNewsForm->createView(),
                'articles' => $articles,
            ]);

        }

        //returning a view for not logged in users

        return $this->render("/home/homepage.html.twig", [
            'lastInsertedPresentations' => $lastInsertedPresentations,
            'setGoalForm' => $setGoalForm->createView(),
            'articles' => $articles,
        ]);

    }

    

}

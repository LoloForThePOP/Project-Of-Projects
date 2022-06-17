<?php

namespace App\Controller;

use App\Service\UluleAPI;
use Algolia\SearchBundle\SearchService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{

    
    /**
     * @Route("/", name="homepage")
     */
    public function index(EntityManagerInterface $manager): Response
    {
             
        /* UluleAPI $ulule,
        $ulule->fetchProjectInfo(); just testing Ulule api*/


        // last 20 inserted projects presentations

        $lastInsertedPresentations = $manager->createQuery('SELECT p FROM App\Entity\PPBase p WHERE p.isPublished=true AND p.overallQualityAssessment>=2 AND p.isAdminValidated=true AND p.isDeleted=false ORDER BY p.createdAt DESC')->setMaxResults('30')->getResult();

        return $this->render("/home/homepage.html.twig", [
            'lastInsertedPresentations' => $lastInsertedPresentations,
        ]);

    }

    
    /**
     * 
     * Test something
     * 
     * @Route("/test-something", name="test_something")
     */
     public function test(): Response
    {

        return $this->render("/test_something.html.twig", [
            
        ]);

    }




}

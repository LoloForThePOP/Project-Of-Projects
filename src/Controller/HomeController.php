<?php

namespace App\Controller;

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

        // last 20 inserted projects presentations

        $lastInsertedPresentations = $manager->createQuery('SELECT p FROM App\Entity\PPBase p WHERE p.isPublished=true AND p.overallQualityAssessment>=2 AND p.isAdminValidated=true AND p.isDeleted=false ORDER BY p.createdAt DESC')->setMaxResults('10')->getResult();

        return $this->render("/home/homepage.html.twig", [
            'lastInsertedPresentations' => $lastInsertedPresentations,
        ]);
    }


    /** 
     * 
     * Allow admin to manage homepage content
     *  
     * @Route("/admin/manage/homepage-content", name="manage_homepage_content") 
     * 
     */
    public function adminManageHomepageContent(Request $request, EntityManagerInterface $manager)
    {

        if ($request->isXmlHttpRequest()) {

            $jsonElementsPosition = $request->request->get('jsonElementsPosition');
            $elementsPosition = json_decode($jsonElementsPosition,true);

            foreach ($categories as $element){

                $newElementPosition = array_search($element->getId(), $elementsPosition, false);
                
                $element->setPosition($newElementPosition);

            }

            $manager->flush();

            return  new JsonResponse(true);

        }
        
        return $this->render("/home/manage_content.html.twig", [
        ]);
        
    }






}

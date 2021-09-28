<?php

namespace App\Controller;

use App\Entity\ImageUpload;
use App\Form\ImageUploadType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
     * @Route("/test", name="test")
     */
    public function test(Request $request, EntityManagerInterface $manager): Response
    {

        $imageUpload = new ImageUpload();

        $form = $this->createForm(ImageUploadType::class, $imageUpload);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager->persist($imageUpload);
            $manager->flush();

            $this->addFlash(
                'success',
                "✅ Image ajoutée"
            );

            return $this->redirectToRoute(
                'homepage',
                [

                ]
            );
        }

        return $this->render('test.html.twig', [
            'form' => $form->createView(),
        ]);
    }




}

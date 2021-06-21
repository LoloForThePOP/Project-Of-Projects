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
    public function index(): Response
    {
        return $this->render('home/homepage.html.twig', []);
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

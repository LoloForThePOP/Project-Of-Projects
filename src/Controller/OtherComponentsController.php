<?php

namespace App\Controller;

use App\Entity\PPBase;
use App\Form\WebsiteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OtherComponentsController extends AbstractController
{

    protected $otherComponentsFamily = ['websites'];


    /**
     * @Route("/projects/{stringId}/{component_type}/", name="manage_oc")
     */
    public function manage(PPBase $presentation, $component_type, Request $request, EntityManagerInterface $manager): Response
    {

        $this->denyAccessUnlessGranted('edit', $presentation);

        if (in_array ($component_type, $this->otherComponentsFamily)) {

            $addWebsiteForm = $this->createForm(WebsiteType::class);

            $addWebsiteForm->handleRequest($request);
    
            if ($addWebsiteForm->isSubmitted() && $addWebsiteForm->isValid()) {
    
                $manager->persist($presentation);
                $manager->flush();
    
                $this->addFlash(
                    'success',
                    "✅ Site web ajoutée"
                );
            }

            // add website form
            
            return $this->redirectToRoute(
                'manage_oc',

                [

                    'stringId' => $presentation->getStringId(),
                    'component_type' => 'websites',

                ]
            );

        return $this->render('project_presentation/edit/websites/manage.html.twig', [
            'presentation' => $presentation,
            'stringId' => $presentation->getStringId(),
            'addWebsiteForm' => $addWebsiteForm->createView(),
        ]);        
    }

    }
}

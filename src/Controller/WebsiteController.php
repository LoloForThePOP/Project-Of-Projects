<?php

namespace App\Controller;

use App\Entity\PPBase;
use App\Form\WebsiteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WebsiteController extends AbstractController
{
    /**
     * @Route("/projects/{stringId}/websites/", name="manage_websites")
     */
    public function manage(PPBase $presentation, Request $request, EntityManagerInterface $manager): Response
    {

        $this->denyAccessUnlessGranted('edit', $presentation);

        // add website form

        $addWebsiteForm = $this->createForm(WebsiteType::class);

        $addWebsiteForm->handleRequest($request);

        if ($addWebsiteForm->isSubmitted() && $addWebsiteForm->isValid()) {

            $manager->persist($presentation);
            $manager->flush();

            $this->addFlash(
                'success',
                "✅ Site web ajoutée"
            );

            return $this->redirectToRoute(
                'manage_websites',

                [

                    'stringId' => $presentation->getStringId(),

                ]
            );
        }

        return $this->render('project_presentation/edit/websites/manage.html.twig', [
            'presentation' => $presentation,
            'stringId' => $presentation->getStringId(),
            'addWebsiteForm' => $addWebsiteForm->createView(),
        ]);
    }
}

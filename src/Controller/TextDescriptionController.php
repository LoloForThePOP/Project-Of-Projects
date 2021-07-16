<?php

namespace App\Controller;

use App\Entity\PPBase;
use App\Entity\PPBasic;
use App\Entity\TextDescription;
use App\Form\TextDescriptionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class TextDescriptionController extends AbstractController
{
    /**
     * 
     * @Route("/projects/{stringId}/edit/text-description", name="edit_pp_text_description")
     * 
     */
    public function edit(PPBase $presentation, Request $request, EntityManagerInterface $manager)
    {

        $this->denyAccessUnlessGranted('edit', $presentation);

        $form = $this->createForm(TextDescriptionType::class, $presentation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($presentation);

            $manager->flush();

            $this->addFlash(
                'success',
                "La description texte a été modifiée"
            );

            return $this->redirectToRoute('show_presentation', [
                'stringId' => $presentation->getStringId(),
                '_fragment' => 'text_description',
            ]);
        }

        return $this->render(

            'project_presentation/edit/text_description/edit.html.twig',
            [
                'form' => $form->createView(),
                'stringId' => $presentation->getStringId(),
            ]
        );
    }
}

<?php

namespace App\Controller;

use App\Entity\PPBase;
use App\Entity\PPBasic;
use App\Service\AssessQuality;
use App\Entity\TextDescription;
use App\Form\TextDescriptionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class TextDescriptionController extends AbstractController
{

    
    /**
     * 
     * @Route("/projects/{stringId}/edit/text-description", name="edit_pp_text_description")
     * 
     */
    public function edit(PPBase $presentation, Request $request, AssessQuality $assessQuality, EntityManagerInterface $manager)
    {

        $this->denyAccessUnlessGranted('edit', $presentation);

        $form = $this->createForm(TextDescriptionType::class, $presentation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $assessQuality->assessQuality($presentation);          

            $manager->flush();

            $this->addFlash(
                'success',
                "✅ Modification effectuée"
            );

            return $this->redirectToRoute('show_presentation', [
                'stringId' => $presentation->getStringId(),
                '_fragment' => 'text-description-struct-container',
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

    /**
     * 
     * @Route("/projects/{stringId}/edit/editorial-text-description", name="edit_pp_editorial_text_description")
     * 
     */
    public function editorial(PPBase $presentation, Request $request, EntityManagerInterface $manager)
    {

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createFormBuilder()
            ->add('editorial', TextareaType::class,
            [
                'required'     => false,
                'data' => $presentation->getDataItem('short_editorial_text_fr'),
                'attr' => [
                    'class' => "tinymce",
                ],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $editorial = $form->get('editorial')->getData();

            $presentation->setDataItem('short_editorial_text_fr', $editorial);

            $manager->flush();

            $this->addFlash(
                'success',
                "✅ Modification effectuée"
            );

            return $this->redirectToRoute('show_presentation', [
                'stringId' => $presentation->getStringId(),
                '_fragment' => 'text-description-struct-container',
            ]);
        }

        return $this->render(

            'project_presentation/edit/text_description/editorial.html.twig',
            [
                'form' => $form->createView(),
                'stringId' => $presentation->getStringId(),
            ]
        );
    }




}

<?php

namespace App\Controller;

use App\Entity\Need;
use App\Entity\PPBase;
use App\Form\NeedType;
use App\Repository\NeedRepository;
use App\Repository\PPBaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NeedController extends AbstractController
{
        
    /**
     * @Route("/projects/{stringId}/needs/new/{need_type}", name="add_need", methods={"GET","POST"})
     * 
     */
    public function new(PPBaseRepository $repo, $stringId, $need_type, Request $request, EntityManagerInterface $manager): Response
    {

        $presentation = $repo->findOneByStringId($stringId);

        $this->denyAccessUnlessGranted('edit', $presentation);

        $need = new Need();

        $form = $this->createForm(NeedType::class, $need)->remove('type'); //need type is selected in previous page

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $need->setType($need_type)
                 ->setPresentation($presentation);

            $manager->persist($need);
            $manager->flush();

            return $this->redirectToRoute('show_presentation', [
                'stringId' => $presentation->getStringId(),
                '_fragment' => 'needs-struct-container',
            ]);

        }

        return $this->render('project_presentation/edit/needs/new.html.twig', [
            'stringId' => $presentation->getStringId(),
            'form' => $form->createView(),
            'needType' => $need_type,
        ]);
    }

    /**
    * @Route("/projects/{stringId}/needs/update/{need_id}", name="update_need", methods={"GET","POST"}) 
    */
    public function update(PPBase $presentation, Request $request, $need_id, NeedRepository $needRepo): Response
    {

        $this->denyAccessUnlessGranted('edit', $presentation);

        $need = $needRepo->findOneById($need_id);

        if ($need->getPresentation() == $presentation) {

            $form = $this->createForm(NeedType::class, $need);

            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();
    
                return $this->redirectToRoute('show_presentation',[
                    'stringId' => $presentation->getStringId(),
                    '_fragment' => 'needs-struct-container',
                ]);
            }
        }

        return $this->render('project_presentation/edit/needs/update.html.twig', [
            'need' => $need,
            'needType' => $need->getType(),
            'form' => $form->createView(),
            'stringId' => $presentation->getStringId(),
        ]);
    }









}

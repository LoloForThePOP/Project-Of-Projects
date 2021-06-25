<?php

namespace App\Controller;

use App\Entity\PPBase;
use App\Entity\Persorg;
use App\Form\PersorgType;
use App\Entity\ContributorStructure;
use App\Form\ContributorStructureType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ContributorStructureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * External Contributors are Sponsors; Donator; Partners ...
 * 
 */
class ECSController extends AbstractController
{

    /**
     * Allow to Access ui CRUD operations for External Contributors Structures (ui to create; delete; reorder an ECS)
     * 
     * @Route("/projects/{stringId}/external-contributor-structures/manage", name="manage_all_ecs")
     * 
     */
    public function manageAllECS (PPBase $presentation, Request $request, EntityManagerInterface $manager)
    {

        $this->denyAccessUnlessGranted('edit', $presentation);

        /* Add an ecs capability */

        $ecs = new ContributorStructure();

        $ecsForm = $this->createForm(ContributorStructureType::class, $ecs);
    
        $ecsForm->handleRequest($request);

        if ($ecsForm->isSubmitted() && $ecsForm->isValid()){

            $ecs->setType('external');

            $ecs->setPresentation($presentation);

            $manager->persist($ecs);

            $manager->flush();

            $this->addFlash(
                'success',
                "✅ Partie ajoutée. Vous pouvez maintenant la remplir."
            );

            return $this->redirectToRoute('manage_one_cs', [
                'stringId' => $presentation->getStringId(),
                'id_cs' => $ecs->getId(),
            ]);

        }

        return $this->render('project_presentation/edit/ecs/manage_all.html.twig', [

            'ecsForm' => $ecsForm->createView(),
            'stringId' => $presentation->getStringId(),
            'presentation' => $presentation,
            
        ]);

    }

  
    /**
     * Allow to access CRUD operation for a Contributor Structure (ex: add someone in it)
     * 
     * @Route("/projects/{stringId}/contributor-structures/{id_cs}/manage", name="manage_one_cs")
     * 
     */
    public function manageOneCS ($id_cs, PPBase $presentation, Request $request, EntityManagerInterface $manager, ContributorStructureRepository $ecsRepository)
    {

        $this->denyAccessUnlessGranted('edit', $presentation);

        $cs = $ecsRepository->find($id_cs);

        /* Contributor Structure Edit Rich Text Content Form */

        $csForm = $this->createForm(ContributorStructureType::class, $cs);
    
        $csForm->handleRequest($request);

        if ($csForm->isSubmitted() && $csForm->isValid()){

            $manager->flush();

            $this->addFlash(
                'success',
                "✅ Action effectuée"
            );

            return $this->redirectToRoute('show_presentation', [
                'stringId' => $presentation->getStringId(),
                '_fragment' => '',
            ]);

        }

        /* Add a persorg into a CS */

        $persorg = new Persorg();
        
        $persorgForm = $this->createForm(PersorgType::class, $persorg);

        $persorgForm->handleRequest($request);

        if ($persorgForm->isSubmitted() && $persorgForm->isValid()){

            $persorg->setContributorStructure($cs);

            $manager->persist($persorg);

            $manager->flush();

            $this->addFlash(
                'success',
                "✅ Ajout effectué"
            );

            return $this->redirectToRoute('show_presentation', [
                'stringId' => $presentation->getStringId(),
                '_fragment' => '',
            ]);

        }

        return $this->render('project_presentation/edit/ecs/manage_one.html.twig', [

            'cs' => $cs,
            'csForm' => $csForm->createView(),
            'persorgForm' => $persorgForm->createView(),
            'stringId' => $presentation->getStringId(),
            'presentation' => $presentation,
            
        ]);

    }

  




}

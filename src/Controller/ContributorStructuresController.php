<?php

namespace App\Controller;

use App\Entity\PPBase;
use App\Entity\Persorg;
use App\Form\PersorgType;
use App\Entity\ContributorStructure;
use App\Repository\PersorgRepository;
use App\Form\ContributorStructureType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ContributorStructureRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * External Contributors are Sponsors; Donator; Partners ...
 * 
 */
class ContributorStructuresController extends AbstractController
{

    /**
     * Allow to Access ui CRUD operations for External Contributors Structures (ui to create; delete; reorder an ECS (= sponsors; partners; donator; etc.))
     * 
     * @Route("/projects/{stringId}/external-contributor-structures/manage", name="manage_all_ecs")
     * 
     */
    public function manageAllECS (PPBase $presentation, Request $request, EntityManagerInterface $manager)
    {

        $this->denyAccessUnlessGranted('edit', $presentation);

        /* Getting Presentation External Contributor Structures */

        $ecs = $presentation->getContributorStructuresBytype('external');

        /* Add a new ecs capability */

        $newECS = new ContributorStructure();

        $ecsForm = $this->createForm(ContributorStructureType::class, $newECS);
    
        $ecsForm->handleRequest($request);

        if ($ecsForm->isSubmitted() && $ecsForm->isValid()){

            $newECS->setType('external');

            $newECS->setPresentation($presentation);

            $manager->persist($newECS);

            $manager->flush();

            $this->addFlash(
                'success',
                "✅ Partie ajoutée. Vous pouvez maintenant la remplir."
            );

            return $this->redirectToRoute('manage_one_cs', [
                'stringId' => $presentation->getStringId(),
                'id_cs' => $newECS->getId(),
            ]);

        }

        return $this->render('project_presentation/edit/contributor_structures/ecs/manage_all.html.twig', [

            'ecsForm' => $ecsForm->createView(),
            'stringId' => $presentation->getStringId(),
            'ecs' => $ecs,
            
        ]);

    }

    /**
     * Allow to update an ECS title
     * 
     * @Route("/projects/{stringId}/external-contributor-structure/{id_ecs}/update-title", name="update_ecs_title")
     * 
     */
    public function updateECSTitle (PPBase $presentation, ContributorStructure $ecs, Request $request, EntityManagerInterface $manager)
    {

        $this->denyAccessUnlessGranted('edit', $presentation);

        $form = $this->createForm(ContributorStructureType::class, $ecs);
    
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $manager->flush();

            $this->addFlash(
                'success',
                "✅ Modification effectuée."
            );

            return $this->redirectToRoute('show_presentation', [
                'stringId' => $presentation->getStringId(),
                '_fragment' => "cs-".$ecs->getId(),
            ]);

        }

        return $this->render('project_presentation/edit/contributor_structures/ecs/update_title.html.twig', [

            'form' => $form->createView(),
            'stringId' => $presentation->getStringId(),
            'csId' => $ecs->getId(),
            
        ]);

    }

  
    /**
     * Allow to access CRUD operation concerning a Contributor Structure (ex: add someone in it)
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
                '_fragment' => 'cs-'.$cs->getId(),
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
                '_fragment' => 'cs-'.$cs->getId(),
            ]);

        }

        return $this->render('project_presentation/edit/contributor_structures/manage_one.html.twig', [

            'cs' => $cs,
            'csForm' => $csForm->createView(),
            'persorgForm' => $persorgForm->createView(),
            'stringId' => $presentation->getStringId(),
            'presentation' => $presentation,
            
        ]);

    }


    /**
     * Allow to Edit a Persorg
     * 
     * @Route("/projects/{stringId}/contributor-structures/persorgs/edit/{id_persorg}", name="edit_persorg")
     * 
     */
    public function editPersorg (PPBase $presentation, $id_persorg, PersorgRepository $persorgRepository, Request $request, EntityManagerInterface $manager)
    {

        $this->denyAccessUnlessGranted('edit', $presentation);
   
        $persorg = $persorgRepository->findOneById($id_persorg);

        $form = $this->createForm(PersorgType::class, $persorg);
    
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $manager->persist($persorg);

            $manager->flush();

            $this->addFlash(
                'success',
                "✅ modifications effectuées"
            );

            return $this->redirectToRoute('show_presentation', [
                'stringId' => $presentation->getStringId(),
                '_fragment' => 'cs-'.$persorg->getContributorStructure()->getId(),
            ]);

        }
    
        return $this->render('/persorgs/update_form.html.twig', [
            'form' => $form->createView(),
            'persorg' => $persorg,
            'stringId' => $presentation->getStringId(),
            'context' => '',
        ]);
    }

    /**
     * Allow to reorder persorgs in a Contributor Structure
     * 
     * @Route("/projects/{stringId}/contributor-structures/ajax-reorder", name="ajax_reorder_cs_persorgs")
     * 
     */
    public function ajaxReorder (PPBase $presentation, ContributorStructureRepository $csRepo, Request $request, EntityManagerInterface $manager)
    {

        $this->denyAccessUnlessGranted('edit', $presentation);
   
        if ($request->isXmlHttpRequest()) {

            $jsonElementsPosition = $request->request->get('jsonElementsPosition');
            $elementsPosition = json_decode($jsonElementsPosition,true);
            $parentContributorStructureId = $request->request->get('parentStructureId');
            
            $parentStructure = $csRepo->findOneById($parentContributorStructureId);

            //dump($parentStructure);

            if ($presentation->getContributorStructures()->contains($parentStructure)) {

                
                foreach ($parentStructure->getPersorgs() as $element){

                    $newElementPosition = array_search($element->getId(), $elementsPosition, false);
                    
                    $element->setPosition($newElementPosition);

                }    
            }

            $manager->flush();

            return  new JsonResponse(true);

        }

        return  new JsonResponse();
    }

  

    /**
     * Allow to remove a persorg in a Contributor Structure
     * 
     * @Route("/projects/{stringId}/contributor-structures/ajax-remove-persorg", name="ajax_remove_cs_persorg")
     * 
     */
    public function ajaxRemove (PPBase $presentation, PersorgRepository $persorgRepo, Request $request, EntityManagerInterface $manager)
    {

        $this->denyAccessUnlessGranted('edit', $presentation);

        $idElement = $request->request->get('idElement');

        if ($request->isXmlHttpRequest()) {

            $persorg = $persorgRepo->findOneById($idElement);
            
            if ($persorg->getContributorStructure()->getPresentation() == $presentation) {

                $manager->remove($persorg);

                $manager->flush();
            }

            $dataResponse = [
            ];

            return new JsonResponse($dataResponse);
        }
    }

  




}

<?php

namespace App\Controller;

use App\Entity\PPBase;
use App\Repository\PPBaseRepository;
use App\Service\TreatOtherComponentItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * Allow to CRUD some components of a presentation.
 * Exemples : CRUD presentation websites; q&a; polls; etc. 
 */

class OtherComponentsController extends AbstractController
{

    protected $managedComponents = ['websites', 'dataList', 'questionsAnswers', 'businessCards'];


    // allow to get the appropriate form fully qualified name (no plural and first letter upper cased)

    public function formName($component_type)
    {
        
        switch ($component_type) {
            case 'dataList':
                $convention = 'DataList';
                break;
            case 'questionsAnswers':
                $convention = 'QuestionAnswer';
                break;
            case 'websites':
            case 'businessCards':
                $convention = $convention = ucfirst(substr($component_type, 0, -1));
                break;
            
            default:
                # code...
                break;
        }

        return 'App\\Form\\'.$convention.'Type';

    }


    /**
     * 
     * Allow to access CRUD operations page
     * 
     * @Route("/projects/{stringId}/other-components/{component_type}/", name="manage_other_components")
     */
    public function manage(PPBase $presentation, $component_type, Request $request, TreatOtherComponentItem $specificTreatments, EntityManagerInterface $manager): Response
    {

        $this->denyAccessUnlessGranted('edit', $presentation);

        if (in_array ($component_type, $this->managedComponents)) {

            $form = $this->createForm($this->formName($component_type));

            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {

                // creating new component item and giving him an id
                // (exemple : creating a website item)

                $componentItem = $form->getData();

                // specific treatments for new item
                // exemple : if new item is a website, we check if we got a logo to attach with it

                $componentItem = $specificTreatments->specificTreatments($component_type, $componentItem);

                $presentation->addOtherComponentItem($component_type, $componentItem);
                $manager->flush();
    
                $this->addFlash(
                    'success',
                    "✅ Ajout effectué"
                );
                
                return $this->redirectToRoute(
                    'manage_other_components',
    
                    [
    
                        'stringId' => $presentation->getStringId(),
                        'component_type' => $component_type,
    
                    ]
                );
            }


        }
        
        return $this->render('project_presentation/edit/other_components/'.$component_type.'/manage.html.twig', [
            'presentation' => $presentation,
            'stringId' => $presentation->getStringId(),
            'form' => $form->createView(),
        ]);        
    }

    /**
     * 
     * Allow to update a component item
     * 
     * @Route("/projects/{stringId}/other_components/{component_type}/{item_id}", name="update_other_components_item")
     */
    public function update(PPBase $presentation, $component_type, $item_id, Request $request, TreatOtherComponentItem $specificTreatments, EntityManagerInterface $manager): Response
    {

        $this->denyAccessUnlessGranted('edit', $presentation);

        if (in_array ($component_type, $this->managedComponents)) {

            // getting item to modify
            
            $itemToUpdate = $presentation->getOCItem($component_type, $item_id);

            $form = $this->createForm($this->formName($component_type), $itemToUpdate);

            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {

                $updatedItem = $form->getData();

                // making some specific treatments according to item type (ex : if item is website, we check if we got a logo to attach with it)

                $updatedItem = $specificTreatments->specificTreatments($component_type, $updatedItem);

                // updating item
    
                $presentation->setOCItem($component_type, $item_id, $updatedItem);

                $manager->flush();
    
                $this->addFlash(
                    'success',
                    "✅ Modification Effectuée"
                );
                
                return $this->redirectToRoute(
                    'show_presentation',
    
                    [
    
                        'stringId' => $presentation->getStringId(),
                        '_fragment' => $component_type,
    
                    ]
                );
            }


            return $this->render('project_presentation/edit/other_components/'.$component_type.'/update.html.twig', [
                'stringId' => $presentation->getStringId(),
                'form' => $form->createView(),
            ]); 
        }

        return $this->redirectToRoute('homepage');               
    }


    /**
     * Allow to reorder items of a presentation component (exemple : reorder presentation websites)
     *
     * @Route("/projects/{stringId}/other_component/ajax-reorder-items/", name="ajax_reorder_items", priority=1)
     * 
    */ 
    public function ajaxReorderItems(Request $request, PPBase $presentation, EntityManagerInterface $manager) {

        $this->denyAccessUnlessGranted('edit', $presentation);

        if ($request->isXmlHttpRequest()) {

            $component_type = $request->request->get('entityType');

            $jsonElementsPosition = $request->request->get('jsonElementsPosition');
            $elementsPosition = json_decode($jsonElementsPosition,true);

            $presentation->positionOtherComponentItem($component_type, $elementsPosition);

            $manager->flush();

            return  new JsonResponse(true);

        }

        return  new JsonResponse();

    }

    /**
     * 
     * Allow to delete an item (ex: delete a website)
     * 
     * @Route("/projects/{stringId}/other_component/ajax-delete-item/", name="ajax_delete_item", priority=2)
     */
    public function delete(PPBase $presentation, Request $request, EntityManagerInterface $manager): Response
    {

        $this->denyAccessUnlessGranted('edit', $presentation);

        if ($request->isXmlHttpRequest()) {

            $component_type = $request->request->get('elementsType');
            $idElement = $request->request->get('idElement');

            $presentation->deleteOtherComponentItem($component_type, $idElement);

            $manager->flush();

            $dataResponse = [
            ];

            return new JsonResponse($dataResponse);

        }

        
    }
 


}

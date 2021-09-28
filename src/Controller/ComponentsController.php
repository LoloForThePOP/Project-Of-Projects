<?php

namespace App\Controller;

use App\Entity\PPBase;
use App\Service\AssessQuality;
use App\Service\CacheThumbnail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ComponentsController extends AbstractController
{

    
    /**
     * Allow to reorder elements of a presentation component (ex: documents) with an ajax request
     *
     * @Route("/projects/{stringId}/component/ajax-reorder-elements/", name="ajax_reorder_component_elements")
     * 
    */ 
    public function ajaxReorderComponentElements(Request $request, PPBase $presentation, EntityManagerInterface $manager) {

        $this->denyAccessUnlessGranted('edit', $presentation);

        if ($request->isXmlHttpRequest()) {

            $entityType = $request->request->get('entityType');

            $jsonElementsPosition = $request->request->get('jsonElementsPosition');

            $elementsPosition = json_decode($jsonElementsPosition,true);


            // getting appropriate getter (function as a variable)

            $methodName = "get".ucfirst($entityType);

            //dump($methodName);

            foreach ($presentation->$methodName() as $element){

                $newElementPosition = array_search($element->getId(), $elementsPosition, false);
                
                $element->setPosition($newElementPosition);

            }

            $manager->flush();

            return  new JsonResponse(true);

        }

        return  new JsonResponse();

    }

     /**
     * Allow to remove an element (ex: a presentation document) (with an ajax request)
     * 
     * @Route("/projects/{stringId}/component/ajax-remove-element/", name="ajax_remove_component_element")
     * 
     */
    public function ajaxRemoveComponentElement(PPBase $presentation, Request $request, EntityManagerInterface $manager, CacheThumbnail $cacheThumbnail, AssessQuality $assessQuality){

        $this->denyAccessUnlessGranted('edit', $presentation);

        if ($request->isXmlHttpRequest()) {

            $entityType = $request->request->get('elementsType');
            $idElement = $request->request->get('idElement');

            // getting appropriate repository
            // fully qualified entity name

            $entityName = 'App\\Entity\\'.ucfirst(substr($entityType, 0, -1));

            $element = $this->getDoctrine()->getRepository($entityName)->findOneById($idElement);

            // getting appropriate getter (function as a variable)

            $getterMethodName = "get".ucfirst($entityType);

            if ($presentation->$getterMethodName()->contains($element)) {

                // getting appropriate remover (function as a variable)

                $removerMethodName = "remove".ucfirst(substr($entityType, 0, -1));

                $presentation-> $removerMethodName($element);
                
                $manager->remove($element);

                $manager->flush();

                if ($entityType="slides") {
                    $cacheThumbnail->cacheThumbnail($presentation);
                    $assessQuality->assessQuality($presentation);  
                }
            }

            $dataResponse = [
            ];

            return new JsonResponse($dataResponse);

        }

    }

}

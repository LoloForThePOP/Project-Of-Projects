<?php

namespace App\Controller;

use App\Entity\PPBase;
use App\Entity\Document;
use App\Form\DocumentType;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DocumentsController extends AbstractController
{
 
    
    /**
     * 
     * Allow user to access CRUD operations (upload a document; delete; update; etc)
     * 
     * @Route("/projects/{stringId}/documents/manage", name="manage_documents")
     */
    public function manage (PPBase $presentation, Request $request, EntityManagerInterface $manager)
    {

        $this->denyAccessUnlessGranted('edit', $presentation);

        $document = new Document();
        
        $form = $this->createForm(DocumentType::class, $document);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $document->setPresentation($presentation);

            $manager->persist($document);

            $manager->flush();

            $idDocument = $document->getId();

            $this->addFlash(
                'success',
                "✅ Ajout effectué"
            );

            return $this->redirectToRoute('manage_documents', [
                'stringId' => $presentation->getStringId(),
                'presentation' => $presentation,
            ]);

        }

        return $this->render('project_presentation/edit/documents/manage.html.twig', [
            'form' => $form->createView(),
            'stringId' => $presentation->getStringId(),
            'presentation' => $presentation,
        ]);

    }

    /**
     * Allow to Edit a Document
     * 
     * @Route("/projects/{stringId}/documents/edit/{id_document}", name="update_document")
     * 
     */
    public function edit (PPBase $presentation, $idDocument, DocumentRepository $documentRepo, Request $request, EntityManagerInterface $manager)
    {

        $this->denyAccessUnlessGranted('edit', $presentation);

        $document = $documentRepo->findOneById($idDocument);

        $form = $this->createForm(DocumentType::class, $document)->remove('file');
    
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $document->setPresentation($presentation);

            $manager->flush();

            $this->addFlash(
                'success',
                "✅ Modification Effectué"
            );

            return $this->redirectToRoute('manage_documents', [
                'stringId' => $presentation->getStringId(),
                'presentation' => $presentation,
            ]);

        }
    
        return $this->render('project_presentation/edit/documents/edit.html.twig', [
            'form' => $form->createView(),
            'stringId' => $presentation->getStringId(),
            'presentation' => $presentation,
        ]);
    }

    
    /**
     * Allow to reorder elements of a presentation component (ex: documents) with an ajax request
     *
     * @Route("/projects/{stringId}/component/ajax-reorder-elements/", name="ajax_reorder_component_elements")
     * 
    */ 
    public function ajaxReorderElements(Request $request, PPBase $presentation, EntityManagerInterface $manager) {

        $this->denyAccessUnlessGranted('edit', $presentation);

        if ($request->isXmlHttpRequest()) {

            $entityType = $request->request->get('entityType');

            $jsonElementsPosition = $request->request->get('jsonDocumentsPosition');

            $elementsPosition = json_decode($jsonElementsPosition,true);


            // getting appropriate getter (function as a variable)

            $methodName = "get".ucfirst($entityType);

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
    public function ajaxRemoveElement(PPBase $presentation, Request $request, EntityManagerInterface $manager){

        $this->denyAccessUnlessGranted('edit', $presentation);

        if ($request->isXmlHttpRequest()) {

            $entityType = $request->request->get('entityType');
            $idElement = $request->request->get('idElement');


            // fully qualified entity name

            $entityName = 'App\\Entity\\'.ucfirst(substr($entityType, 0, -1)).'Type';

            $element = $this->getDoctrine()->getRepository($entityName)->findOneById($idElement);

            // getting appropriate getter (function as a variable)

            $getterMethodName = "get".ucfirst($entityType);


            if ($presentation->$getterMethodName()->contains($element)) {

                // getting appropriate remover (function as a variable)

                $removerMethodName = "remove".ucfirst($entityType)."s";

                $presentation-> $removerMethodName($element);
                
                $manager->remove($element);

                $manager->flush();
            }

            $dataResponse = [
            ];

            return new JsonResponse($dataResponse);

        }

    }

    
}

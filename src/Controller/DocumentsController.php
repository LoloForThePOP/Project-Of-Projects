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
     * @Route("/projects/{stringId}/documents/edit/{id_element}", name="update_document")
     * 
     */
    public function update (PPBase $presentation, $id_element, DocumentRepository $documentRepo, Request $request, EntityManagerInterface $manager)
    {

        $this->denyAccessUnlessGranted('edit', $presentation);

        $document = $documentRepo->findOneById($id_element);

        $form = $this->createForm(DocumentType::class, $document);
        /*->remove('file')*/
    
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
    
        return $this->render('project_presentation/edit/documents/update.html.twig', [
            'form' => $form->createView(),
            'stringId' => $presentation->getStringId(),
            'presentation' => $presentation,
        ]);
    }

    /*
     * reorder documents; 
     * delete document
     * 
     * -> see ComponentsController
     * 
    */

    
}

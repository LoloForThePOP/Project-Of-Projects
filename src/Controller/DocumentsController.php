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
     * Allow to Edit a Document
     * 
     * @Route("/projects/{stringId}/documents/edit/{id_element}", name="update_document")
     * 
     */
    public function update (PPBase $presentation, $id_element, DocumentRepository $documentRepo, Request $request, EntityManagerInterface $manager)
    {

        $this->denyAccessUnlessGranted('edit', $presentation);

        $document = $documentRepo->findOneById($id_element);

        if ($document->getPresentation()==$presentation) {

            $form = $this->createForm(DocumentType::class, $document);
            
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()){
    
                $document->setPresentation($presentation);
    
                $manager->flush();
    
                $this->addFlash(
                    'success',
                    "✅ Modification Effectué"
                );
    
                return $this->redirectToRoute('show_presentation', [
                    'stringId' => $presentation->getStringId(),
                    '_fragment' => 'documents',
                ]);
    
            }
        }
    
        return $this->render('project_presentation/edit/documents/update.html.twig', [
            'form' => $form->createView(),
            'stringId' => $presentation->getStringId(),
            'document' => $document,
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

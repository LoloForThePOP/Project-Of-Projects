<?php

namespace App\Controller;

use App\Entity\PPBase;
use App\Form\PPKeywordsType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{

    /**
     * Allow user to :
     * 
     *  - display and select Project Categories
     *  - display and edit Project Keywords
     * 
     * @Route("/projects/{stringId}/categories", name="select_categories")
     * 
     */
    public function select(PPBase $presentation, Request $request, EntityManagerInterface $manager, CategoryRepository $categoryRepository)
    {

        $this->denyAccessUnlessGranted('edit', $presentation);

        $categories = $categoryRepository->findBy([], ['position' => 'ASC']);

        $keywordsForm = $this->createForm(PPKeywordsType::class, $presentation);

        $keywordsForm->handleRequest($request);

        if ($keywordsForm->isSubmitted() && $keywordsForm->isValid()) {

            $manager->persist($presentation);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les mots-clés du Projet ont été mis à jour"
            );

            return $this->redirectToRoute('show_presentation', [
                'stringId' => $presentation->getStringId(),
            ]);
        }

        return $this->render('project_presentation/edit/categories/select.html.twig', [
            'categories' => $categories,
            'presentation' => $presentation,
            'stringId' => $presentation->getStringId(),
            'keywordsForm' => $keywordsForm->createView(),
        ]);
    }


    /** 
     *  
     * @Route("/projects/{stringId}/ajax-select-category", name="ajax_select_category") 
     * 
     */
    public function ajaxSelectCategory(Request $request, PPBase $presentation, CategoryRepository $categoryRepository, EntityManagerInterface $manager)
    {

        $this->denyAccessUnlessGranted('edit', $presentation);

        if ($request->isXmlHttpRequest()) {

            $categoryId = $request->request->get('category-id');

            $category = $categoryRepository->findOneById($categoryId);

            $presentationCategories = $presentation->getCategories();

            if (!$presentationCategories->contains($category)) {
                $presentation->addCategory($category);
            } else {
                $presentation->removeCategory($category);
            }

            $manager->persist($presentation);
            $manager->flush();

            return new Response();
        }
    }
}

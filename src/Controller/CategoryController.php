<?php

namespace App\Controller;

use App\Entity\PPBase;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Form\PPKeywordsType;
use App\Service\AssessQuality;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{

    /**
     * Allow user to :
     * 
     *  - display and select Project Categories
     *  - display and edit Project Keywords
     * 
     * @Route("/projects/{stringId}/categories", name="select_categories_keywords")
     * 
     */
    public function select(PPBase $presentation, Request $request, AssessQuality $assessQuality, EntityManagerInterface $manager, CategoryRepository $categoryRepository)
    {

        $this->denyAccessUnlessGranted('edit', $presentation);

        $categories = $categoryRepository->findBy([], ['position' => 'ASC']);

        $keywordsForm = $this->createForm(PPKeywordsType::class, $presentation);

        $keywordsForm->handleRequest($request);

        if ($keywordsForm->isSubmitted() && $keywordsForm->isValid()) {

            $assessQuality->assessQuality($presentation);  

            $manager->flush();

            $this->addFlash(
                'success',
                "✅ Modifications effectuées"
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
    public function ajaxSelectCategory(Request $request, AssessQuality $assessQuality, PPBase $presentation, CategoryRepository $categoryRepository, EntityManagerInterface $manager)
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

            $assessQuality->assessQuality($presentation);  

            $manager->flush();

            return new JsonResponse(true);
        }

        return new JsonResponse();
    }

    /** 
     *  
     * @Route("/projects/{stringId}/ajax-update-keywords", name="ajax_update_keywords") 
     * 
     */
    public function ajaxUpdateKeywords(Request $request, AssessQuality $assessQuality, PPBase $presentation, EntityManagerInterface $manager)
    {

        $this->denyAccessUnlessGranted('edit', $presentation);

        if ($request->isXmlHttpRequest()) {

            $keywords = $request->request->get('keywords');

            $presentation->setKeywords($keywords);

            $assessQuality->assessQuality($presentation);  

            $manager->flush();

            return new JsonResponse(true);
        }
    }

    /** 
     * 
     * Allow admin to Access project categories reordering, editing button, and new category button
     *  
     * @Route("/admin/categories/manage", name="manage_categories") 
     * 
     */
    public function adminManageCategories(Request $request, CategoryRepository $categoryRepository, EntityManagerInterface $manager)
    {

        $categories= $categoryRepository->findBy([], ['position' => 'ASC']);

        return $this->render('project_presentation/edit/categories/admin/admin_manage.html.twig', [

            'categories' => $categories,
            'admin' => true,

        ]);

    }

    /** 
     * 
     * Allow admin to reorder categories
     *  
     * @Route("/admin/categories/ajax-reorder", name="reorder_categories") 
     * 
     */
    public function adminAjaxReorderCategories(Request $request, CategoryRepository $categoryRepository, EntityManagerInterface $manager)
    {

        if ($request->isXmlHttpRequest()) {

            $categories= $categoryRepository->findAll();

            $jsonElementsPosition = $request->request->get('jsonElementsPosition');
            $elementsPosition = json_decode($jsonElementsPosition,true);

            foreach ($categories as $element){

                $newElementPosition = array_search($element->getId(), $elementsPosition, false);
                
                $element->setPosition($newElementPosition);

            }

            $manager->flush();

            return  new JsonResponse(true);

        }

        return  new JsonResponse();
        
    }

    /** 
     * 
     * Allow admin to Create or Edit a Category
     *  
     * @Route("/admin/categories/edit/{id?}", name="edit_category") 
     * 
     */
    public function adminEditCategory(Request $request, Category $category = null, EntityManagerInterface $manager)
    {
        $context= null;
        $cat_id = null;
        
        if (!$category) {
            $category= new Category();
            $context= 'new';
        }

        else {
            $context = 'update';
            $cat_id= $category->getId();
        }
       
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager->persist($category);
            $manager->flush();

            $this->addFlash(
                'success',
                "✅ Catégorie éditée"
            );

            return $this->redirectToRoute('manage_categories', []);
        }

        return $this->render('project_presentation/edit/categories/admin/admin_edit.html.twig', [
            
            'form' => $form->createView(),
            'context' => $context,
            'cat_id' => $cat_id,

        ]);
        
    }


    /** 
     * 
     * Allow admin to Delete a Category
     *  
     * @Route("/admin/categories/delete/{id}", name="delete_category") 
     * 
     */
    public function adminDeleteCategory(Category $category, EntityManagerInterface $manager)
    {
        
        $manager->remove($category);

        $manager->flush();
       
        return $this->redirectToRoute('manage_categories');
        
    }


}

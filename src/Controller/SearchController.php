<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    /**
     * @Route("/search", name="search")
     */
    public function index(): Response
    {
        return $this->render('search/index.html.twig', [
            'controller_name' => 'SearchController',
        ]);
    }



    /** 
     * @Route("/ajax-search-by-cat", name="ajax_search_by_cat") 
    */ 

    public function ajaxSearchByCat(PPBasicRepository $repo, Request $request) {

        if ($request->isXmlHttpRequest()) {

            //get selected categories

            $jsonSelectedCategoriesIds = $request->request->get('jsonSelectedCategoriesIds');

            $selectedCategoriesIds = json_decode($jsonSelectedCategoriesIds, true);

            //count selected categories

            $countSelectedCategories = $request->request->get('countSelectedCategories');

            //get search results

            $results = $repo->findByCategories($selectedCategoriesIds);

            $dataResponse = [

                'html' => $this->renderView(
                    
                    'search/by_categories_results.html.twig', 

                    [
                        'results' => $results,
                    ]
                ),
            ];

            return new JsonResponse($dataResponse);

        
        }

    }













}

<?php

namespace App\Controller;

use App\Entity\PPBase;
use Algolia\SearchBundle\SearchService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{
    protected $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * @Route("/search", name="search")
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManagerForClass(PPBase::class);

        $results = $this->searchService->search($em, PPBase::class, '', 
            
            [
                'filters' => '(categories.uniqueName:material AND categories.uniqueName:science)'
            ]
        );   

        //dd($results);

        return $this->render('search/index.html.twig', [
            'controller_name' => 'SearchController',
        ]);
    }

    /**
     * Allow to get search results from a twig query string
     * 
     * label : the label displayed on view (ex: "Environmental Projects").
     * 
     * @Route("/backend-search/{label}/{filters}", name="backend_search")
     *
     */
    public function backendSearch($filters='', $label='')
    {

        $em = $this->getDoctrine()->getManagerForClass(PPBase::class);

        $results = $this->searchService->search($em, PPBase::class, '', 
            
            [
                'filters' => $filters,
            ]
        );   

        return $this->render('utilities/_display_collection.html.twig', [
            'label' => $label,
            'results' => $results,
        ]);

    }

}

<?php

namespace App\Controller;

use App\Entity\PPBase;
use Algolia\SearchBundle\SearchService;
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
     * Allow to get search results from a twig query string
     * 
     * label : the label displayed on view (ex: "Environmental Projects").
     * 
     * @Route("/backend-search/{label}/{filters}", name="backend_search")
     *
     */
    public function backendSearch($label='', $filters='')
    {

        $em = $this->getDoctrine()->getManagerForClass(PPBase::class);

        $searchOptions = [];

        if ($filters != '') {
            $searchOptions['filters']= $filters;
        }

        $results = $this->searchService->search($em, PPBase::class, '', $searchOptions);   

        return $this->render('utilities/_display_collection_wrapper_template.html.twig', [
            'label' => $label,
            'results' => $results,
        ]);

    }

}

<?php

namespace App\Controller\Admin;

use App\Repository\PPBaseRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SelectPresentationsController extends AbstractController
{

    protected $storagePath = '../templates/select_presentations/editor_selection.json';
    
   
    /**
     * Allow to manage presentation headlines (i.e. editor's pick for homepage).
     * Pick up some presentations and store them in the website headline.
     * 
     * @Route("/admin/manage-pick-elements", name="manage_pick_elements")
     *
     */
    public function managePickElements(PPBaseRepository $ppRepo)
    {

        $currentSelectionItems = [];

        if ($currentSelectionIds = json_decode(file_get_contents($this->storagePath))) {

            foreach ($currentSelectionIds as $id) { //one by one request to maintain ordering

                $currentSelectionItems[] = $ppRepo->findOneById($id);
    
            }
            
        }

        return $this->render('select_presentations/manage.html.twig', [
            'currentSelection' => $currentSelectionItems,
        ]);

    }

    /** 
     * 
     * Ajax request to store editor's selection
     *  
     * @Route("/admin/ajax-pick-up-elements", name="manage_pick_up_elements") 
     * 
     */
    public function ajaxPickUpElements(Request $request)
    {

        if ($request->isXmlHttpRequest()) {

            $jsonSelection = $request->request->get('jsonElementsPosition');

            file_put_contents($this->storagePath, json_encode($jsonSelection));
           

            return  new JsonResponse(true);

        }

        return  new JsonResponse();
        
    }



    /** 
     * 
     * Allow to get picked elements (return an hysrated collection of selected elements)
     *  
     * @Route("/get-picked-elements/{label}", name="get_picked_elements") 
     * 
     */
    public function getPickedElements(PPBaseRepository $ppRepo, $label='')
    {
        $elements = [];

        if($ids = json_decode(file_get_contents($this->storagePath))){

            foreach ($ids as $id) { //one by one request to maintain ordering

                $elements[] = $ppRepo->findOneById($id);

            }

        }

        return $this->render('utilities/_display_collection_wrapper_template.html.twig', [
            'label' => $label,
            'results' => $elements,
        ]);
        
    }



}

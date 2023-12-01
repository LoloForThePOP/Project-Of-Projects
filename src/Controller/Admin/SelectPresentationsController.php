<?php

namespace App\Controller\Admin;

use App\Repository\PPBaseRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SelectPresentationsController extends AbstractController
{

    protected $genericStoragePath = '../templates/select_presentations/editor_selection.json';
    protected $projectOfTheDay = '../templates/select_presentations/project_of_the_day.json';
    
   
    /**
     * Allow to manage presentation headlines (i.e. editor's pick for homepage).
     * Pick up some presentations and store them in the website headline.
     * 
     * @Route("/admin/manage-pick-elements/{pickType}", name="manage_pick_elements")
     *
     */
    public function managePickElements(PPBaseRepository $ppRepo, string $pickType)
    {


        $storagePath = null;

        switch ($pickType) {
            case 'trustUs':
                $storagePath = $this->genericStoragePath;
                break;
            case 'projectOfTheDay':
                $storagePath = $this->projectOfTheDay;
                break;
            
            default:
                throw new \Exception("Unknow manage presentation selections storage path");
                break;
        }

        $currentSelectionItems = [];

        if ($currentSelectionIds = json_decode(file_get_contents($storagePath))) {

            foreach ($currentSelectionIds as $id) { //one by one request to maintain ordering

                $currentSelectionItems[] = $ppRepo->findOneById($id);
    
            }
            
        }

        return $this->render('select_presentations/manage.html.twig', [
            'currentSelection' => $currentSelectionItems,
            'pickType' => $pickType,
        ]);

    }

    /** 
     * 
     * Ajax request to store editor's selection
     *  
     * @Route("/admin/ajax-pick-up-elements/{pickType}", name="manage_pick_up_elements") 
     * 
     */
    public function ajaxPickUpElements(Request $request, string $pickType)
    {

        if ($request->isXmlHttpRequest()) {

            $storagePath = null;

            switch ($pickType) {
                case 'trustUs':
                    $storagePath = $this->genericStoragePath;
                    break;
                case 'projectOfTheDay':
                    $storagePath = $this->projectOfTheDay;
                    break;
                
                default:
                    throw new \Exception("Unknow manage presentation selections storage path");
                    break;
            }

            $jsonSelection = $request->request->get('jsonElementsPosition');

            file_put_contents($storagePath, json_encode($jsonSelection));
           

            return  new JsonResponse(true);

        }

        return  new JsonResponse();
        
    }



    /** 
     * 
     * Allow to get picked elements (return an hydrated collection of selected elements)
     *  
     * @Route("/get-picked-elements/{pickType}/{label}/{iconName}", name="get_picked_elements") 
     * 
     */
    public function getPickedElements(PPBaseRepository $ppRepo, string $pickType, $label='none', $iconName = '')
    {
        $elements = [];

        $storagePath = null;

        switch ($pickType) {
            case 'trustUs':
                $storagePath = $this->genericStoragePath;
                break;
            case 'projectOfTheDay':
                $storagePath = $this->projectOfTheDay;
                break;
            
            default:
                throw new \Exception("Unknow manage presentation selections storage path");
                break;
        }

        if($ids = json_decode(file_get_contents($storagePath))){

            foreach ($ids as $id) { //one by one request to maintain ordering

                $elements[] = $ppRepo->findOneById($id);

            }

        }

          switch ($pickType) {
            case 'trustUs':
                return $this->render('utilities/_display_collection_wrapper_template.html.twig', [
                    'label' => $label,
                    'iconName' => $iconName,
                    'results' => $elements,
                    'hideTooLong' => "hide-335",
                ]);
                break;

            case 'projectOfTheDay':

                return $this->render('utilities/_display_cool_project_of_the_day.html.twig', [
                    'results' => $elements,
                ]);
                break;
            
            default:
                throw new \Exception("Unknow manage presentation selections storage path");
                break;
        }

       
        
    }



}

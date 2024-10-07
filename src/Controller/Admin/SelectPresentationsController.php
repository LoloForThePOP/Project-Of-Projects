<?php

namespace App\Controller\Admin;

use App\Repository\PPBaseRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * Context: sometimes admins want to manually select and store a specific projects list in order to highlight these projects (ex: highlight some projects on homepage).
 * This controller allows that: select; store and manage some specific project lists + render (display) these lists in html format.
 */
class SelectPresentationsController extends AbstractController
{
    
    /**
     * Getting the path + filename + extension of the project list we want to manage knowing its file name.
     * 
     * Params : (string) fileName: file name of the project list we want to handle
     */
    protected function getListFilePath ($fileName){ 

        $basePath = '../templates/select_presentations/'; // Path where projects lists are stored
        $fileExtension = ".json"; // Project lists are stored in json format (ids of project presentations)

        switch ($fileName) {

            case 'editor_selection': // a list of projects selected by a propon admin
                $fileName = "editor_selection";
                break;

            case 'project_of_the_day': // a list of one project we want to highlight on homepage
                $fileName = "project_of_the_day";
                break;
            
            default:
                throw new \Exception("Manually selected presentations list: unknow filename");
                break;
        }

        return $basePath.$fileName.$fileExtension;

    }


    
    /**
     * Allow an admin to pick up some project presentations; store a list of these projects; and manage this list (reorder the list, remove some elements).
     * 
     * (route parameter) pickType: (string) the name of the list we want to change (ex: project_of_the_day) (which type of projects we pick) 
     * 
     * @Route("/admin/manage-pick-elements/{pickType}", name="manage_pick_elements")
     *
     */
    public function managePickElements(PPBaseRepository $ppRepo, string $pickType)
    {

        $storageFilePath = $this->getListFilePath($pickType);  // Getting the appropriate project list path + filename of the project list we want to manage

        // Getting in database current project list content so that admin can see it and update it
        
        $currentSelectionItems = [];

        if ($currentSelectionIds = json_decode(file_get_contents($storageFilePath))) {// array of selected project ids

            //getting in database selected projects one by one in order to maintin ordering

            foreach ($currentSelectionIds as $id) { 

                $currentSelectionItems[] = $ppRepo->findOneById($id);
    
            }
            
        }

        //Render an user interface so that admin can manage the considered list (crud the list elements)
        return $this->render('select_presentations/manage.html.twig', [
            'currentSelection' => $currentSelectionItems,
            'pickType' => $pickType,
        ]);

    }


    /** 
     * 
     * Ajax request for managePickElements function above so that admin can conveniently update a specific project list
     * 
     * (route parameter) pickType: (string) the name of the list we want to change (ex: project_of_the_day) (which type of projects we pick) 
     *  
     * @Route("/admin/ajax-pick-up-elements/{pickType}", name="manage_pick_up_elements") 
     * 
     */
    public function ajaxPickUpElements(Request $request, string $pickType)
    {

        if ($request->isXmlHttpRequest()) {

            $storageFilePath = $this->getListFilePath($pickType); // Getting the appropriate project list path + filename of the project list we wants to ajax update
            
            // Getting the updated project list from the ajax call
            $jsonSelection = $request->request->get('jsonElementsPosition');

            // Updating the json file representing the project list

            file_put_contents($storageFilePath, json_encode($jsonSelection));

            return  new JsonResponse(true);

        }

        return  new JsonResponse();
        
    }



    /** 
     * Allow to display a previously selected project list in html format. This route can be directly called from a twig template to directly get and display the list of projects we want from a twig template.
     * 
     * picktype: (string) the name of list you want to render
     * label: (string) a title you want to give to the project list (this title will appear in frontend above the list)
     * iconname: (string) (name of an icon without its file format) a frontend icon you might want to add before the list title
     *  
     * @Route("/get-picked-elements/{pickType}/{label}/{iconName}", name="get_picked_elements") 
     * 
     */
    public function getPickedElements(PPBaseRepository $ppRepo, string $pickType, $label='none', $iconName = '')
    {
              
        $storagePath = $this->getListFilePath($pickType); // getting the path + filename of the list you want to display

    
        // Getting specific project list in database

        $elements = []; // symfony collection of project list

        if($ids = json_decode(file_get_contents($storagePath))){

            foreach ($ids as $id) { //one by one database request to maintain ordering

                $elements[] = $ppRepo->findOneById($id);

            }

        }

        // Calling a specific twig template to display the specific project list

        switch ($pickType) {

            case 'editor_selection':

                return $this->render('utilities/_display_collection_wrapper_template.html.twig', [
                    'label' => $label,
                    'iconName' => $iconName,
                    'results' => $elements,
                    'hideTooLong' => "hide-335",
                ]);
                break;

            case 'project_of_the_day':

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

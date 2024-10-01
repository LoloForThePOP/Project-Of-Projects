<?php

namespace App\Controller\Admin;

use App\Repository\PPBaseRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * Context: sometimes admins want to select and store a specific projects list in order to highlight these projects (ex: highlight some projects on homepage).
 * This controller allows that: select; store; manage some specific project lists + render (display) these lists in html format.
 */
class SelectPresentationsController extends AbstractController
{
    // Project lists are stored in json format
    
    protected $genericStoragePath = '../templates/select_presentations/editor_selection.json'; // path + filename of the list containing some projects we want to highlight on homepage
    
    protected $projectOfTheDay = '../templates/select_presentations/project_of_the_day.json'; // path + filename of the list containing one project we want to highlight on homepage (project of the day)

    // End of controller variable declarations


    
    /**
     * Allow an admin to pick up some project presentations; store a list of these projects; and manage this list (reorder the list, remove some elements).
     * 
     * (route parameter) pickType: (string) the name of the list we want to change (ex: projectOfTheDay) (which type of projects we pick) 
     * 
     * @Route("/admin/manage-pick-elements/{pickType}", name="manage_pick_elements")
     *
     */
    public function managePickElements(PPBaseRepository $ppRepo, string $pickType)
    {

        $storagePath = null;

        // Getting the path + filename of the project list we want to manage according to the pickType.

        switch ($pickType) {

            case 'trustUs': // "They trust us" (social aprouval on homepage) = editor's selection projects
                $storagePath = $this->genericStoragePath;
                break;

            case 'projectOfTheDay': // We pick up one project we want to highlight on homepage
                $storagePath = $this->projectOfTheDay;
                break;
            
            default:
                throw new \Exception("Unknow manage presentation selections storage path");
                break;
        }

        // Getting in database current project list content so that admin can see it and update it
        
        $currentSelectionItems = [];

        if ($currentSelectionIds = json_decode(file_get_contents($storagePath))) { // if file content is not empty

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
     * (route parameter) pickType: (string) the name of the list we want to change (ex: projectOfTheDay) (which type of projects we pick) 
     *  
     * @Route("/admin/ajax-pick-up-elements/{pickType}", name="manage_pick_up_elements") 
     * 
     */
    public function ajaxPickUpElements(Request $request, string $pickType)
    {

        if ($request->isXmlHttpRequest()) {

            $storagePath = null;

            // Getting the appropriate project list path + filename of the project list we wants to ajax update

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
            
            // Getting the updated project list from the ajax call
            $jsonSelection = $request->request->get('jsonElementsPosition');

            // Updating the json file representing the project list

            file_put_contents($storagePath, json_encode($jsonSelection));

            return  new JsonResponse(true);

        }

        return  new JsonResponse();
        
    }



    /** 
     * Allow to display a selected project list in html format. This route can be directly called from an twig template to directly get and display the list of projects we want from a twig template.
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
              
        // getting the path + filename of the list you want to display

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

        // Getting specific project list in database

        $elements = []; // symfony collection of project list

        if($ids = json_decode(file_get_contents($storagePath))){

            foreach ($ids as $id) { //one by one database request to maintain ordering

                $elements[] = $ppRepo->findOneById($id);

            }

        }

        // Calling a specific twig template to display the specific project list

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

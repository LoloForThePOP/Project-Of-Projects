<?php

namespace App\Controller\Admin;

use App\Repository\PPBaseRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * This controller gathers functions that select and render some specific project lists
 * Ex: admin selects some specific project presentations he wants to highlight on homepage.
 */

class SelectPresentationsController extends AbstractController
{
    // Paths of some specific project list stored in json format

    protected $genericStoragePath = '../templates/select_presentations/editor_selection.json';
    protected $projectOfTheDay = '../templates/select_presentations/project_of_the_day.json';
    
   
    /**
     * Allow an admin to pick up some project presentations; store a list of these projects; manage this list (so that we can reuse that project list later on, for example we highlight a list of projects on homepage)
     * 
     * @Route("/admin/manage-pick-elements/{pickType}", name="manage_pick_elements")
     *
     */
    public function managePickElements(PPBaseRepository $ppRepo, string $pickType)
    {

        $storagePath = null;

        // The path where we get the project list according to the selection criteria

        switch ($pickType) {

            case 'trustUs': // "They trust us" Projects (social aprouval on homepage)
                $storagePath = $this->genericStoragePath;
                break;

            case 'projectOfTheDay': // when we pick up one project we want to highlight on homepage
                $storagePath = $this->projectOfTheDay;
                break;
            
            default:
                throw new \Exception("Unknow manage presentation selections storage path");
                break;
        }

        $currentSelectionItems = [];

        // Getting in database the current specific project list so that admin can see it and update it

        if ($currentSelectionIds = json_decode(file_get_contents($storagePath))) {

            foreach ($currentSelectionIds as $id) { //one by one request to maintain ordering

                $currentSelectionItems[] = $ppRepo->findOneById($id);
    
            }
            
        }

        // Render an admin user interface so that admin can update the considered list

        return $this->render('select_presentations/manage.html.twig', [
            'currentSelection' => $currentSelectionItems,
            'pickType' => $pickType,
        ]);

    }

    /** 
     * 
     * Ajax request so that admin can update a specific project list
     *  
     * @Route("/admin/ajax-pick-up-elements/{pickType}", name="manage_pick_up_elements") 
     * 
     */
    public function ajaxPickUpElements(Request $request, string $pickType)
    {

        if ($request->isXmlHttpRequest()) {

            $storagePath = null;

            // Getting the appropriate project list path

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

            // Updating the json file representing the project list so that we can access it and potentially update it.

            $jsonSelection = $request->request->get('jsonElementsPosition');

            file_put_contents($storagePath, json_encode($jsonSelection));

            return  new JsonResponse(true);

        }

        return  new JsonResponse();
        
    }



    /** 
     * 
     * Allow to get a specific project list and render it in directly displayable html format.
     * This route can be directly called from an twig template so that we directly get and display the list of projects we want.
     * 
     * picktype: (string) the name of list you want to render
     * label: (string) a title you want to give to the project list, users will see it displayed
     * iconname: (string: name of an icon without its file format) an icon you might want to add before the list title
     *  
     * @Route("/get-picked-elements/{pickType}/{label}/{iconName}", name="get_picked_elements") 
     * 
     */
    public function getPickedElements(PPBaseRepository $ppRepo, string $pickType, $label='none', $iconName = '')
    {
        $elements = []; // symfony collection of project list

        $storagePath = null; // specific storage page to get a specific project list stored in json format

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

        if($ids = json_decode(file_get_contents($storagePath))){

            foreach ($ids as $id) { //one by one database request to maintain ordering

                $elements[] = $ppRepo->findOneById($id);

            }

        }

        // Calling specific twig template to display specific project list

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

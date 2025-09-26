<?php

namespace App\Controller;

use App\Entity\PPBase;
use App\Service\CacheThumbnail;
use Doctrine\ORM\EntityManager;
use App\Repository\UserRepository;
use App\Repository\PlaceRepository;
use App\Repository\PPBaseRepository;
use App\Repository\CategoryRepository;
use Algolia\SearchBundle\SearchService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * This controller allows to perform an action that will be executed only once. Exemple : change something in database for all users.
 * 
 * It is only accessed for admins thanks to  /admin/ route
 * 
 * It is safe to delete the method content once it's been applied
 * 
 */
class OneShotController extends AbstractController
{

    /**
     * @Route("/admin/one-shot", name="one_shot")
     * 
     */
    public function doAction(PPBaseRepository $projectsRepo, PlaceRepository $placesRepo, CategoryRepository $categoriesRepo, SearchService $searchService, EntityManagerInterface $manager, CacheThumbnail $cacheThumbnail, UserRepository $userRepo, CacheThumbnail $thumb): Response
    {

        $users=$userRepo->findAll();

        foreach ($users as $user) {

            if(count($user->getCreatedPresentations())==0 && count($user->getPurchases()) == 0 && count($user->getMessages()) == 0  ){

                $manager->remove($user);
                

            }
            
            $manager->flush();
        }




        
        /*$projects=$projectsRepo->findAll();

        foreach ($projects as $project) {
            $cacheThumbnail->cacheThumbnail($project);
        }

         

        $places = $placesRepo->findAll();

        foreach ($places as $place) {

            $place->setGeoloc(
                [
                    "lat" => floatval($place->getGeoloc()["lat"]),
                    "lng" => floatval($place->getGeoloc()["lng"]),
                ]
            );

        }

        
        $manager->flush();

       
        
            $presentations = $repo->findAll();

            $manager = $this->getDoctrine()->getManagerForClass(PPBase::class);
            
            foreach ($presentations as $presentation) {

                $cacheThumbnail->cacheThumbnail($presentation);

            } 
 */ 
       

        
/*             foreach ($presentations as $presentation) {
                $searchService->index($em, $presentation);
            } 
        
            $categories = $categoriesRepo->findAll();

            foreach ($categories as $category) {
                $searchService->index($em, $category);
            }  */
        
/*  
        $places = $placesRepo->findAll();

        foreach ($places as $place) {

            $place->setGeoloc(
                [
                    "lat" => floatval($place->getGeoloc()["lat"]),
                    "lng" => floatval($place->getGeoloc()["lng"]),
                ]
            );

        } */

        //throw new HttpException(500, "Exception sent by email test");

/*         $users=$userRepo->findAll();

        foreach ($users as $user) {

            $user->setDataItem('lastEmailNotificationDate', 0);
            $user->setDataItem('notificationJournalBuffer', []);

        } */

        $manager->flush();

        $this->addFlash(
            'success',
            "✅ L'action one-shot a été effectuée."
        );

        return $this->redirectToRoute('homepage', [
            'controller_name' => 'OneShotController',
        ]);

    }

}

<?php

namespace App\Controller;

use App\Entity\PPBase;
use App\Service\CacheThumbnail;
use Doctrine\ORM\EntityManager;
use App\Repository\PlaceRepository;
use App\Repository\PPBaseRepository;
use App\Repository\CategoryRepository;
use Algolia\SearchBundle\SearchService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
    public function doAction(PPBaseRepository $repo, PlaceRepository $placesRepo, CategoryRepository $categoriesRepo, SearchService $searchService, EntityManagerInterface $manager, CacheThumbnail $cacheThumbnail): Response
    {

        /* 

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

        */ 
       
        
            $presentations = $repo->findAll();

            $manager = $this->getDoctrine()->getManagerForClass(PPBase::class);
            
            foreach ($presentations as $presentation) {

                $cacheThumbnail->cacheThumbnail($presentation);

            } 


        
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

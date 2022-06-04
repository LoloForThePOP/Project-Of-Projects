<?php

namespace App\Controller;

use App\Entity\PPBase;
use App\Service\CacheThumbnail;
use Doctrine\ORM\EntityManager;
use App\Repository\PlaceRepository;
use App\Repository\PPBaseRepository;
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
    public function doAction(PPBaseRepository $repo, PlaceRepository $placesRepo, SearchService $searchService, EntityManagerInterface $manager): Response
    {

        /* 
        
            $presentations = $repo->findAll();

            $em = $this->getDoctrine()->getManagerForClass(PPBase::class);

            foreach ($presentations as $presentation) {
                $searchService->index($em, $presentation);
            } 
        
        */

        $places = $placesRepo->findAll();

        foreach ($places as $place) {

            $place->setGeoloc(
                [
                    "lat" => $place->getLatitude(),
                    "lng" => $place->getLongitude(),
                ]
            );

        } 

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

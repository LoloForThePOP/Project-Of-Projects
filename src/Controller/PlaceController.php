<?php

namespace App\Controller;

use App\Entity\Place;
use App\Entity\PPBase;
use App\Repository\PlaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PlaceController extends AbstractController
{
    /**
     * 
     * Allow to add a place or delete it in a project presentation
     * 
     * @Route("/projects/{stringId}/places", name="manage_places")
     */
    public function index(PPBase $presentation): Response
    {

        $this->denyAccessUnlessGranted('edit', $presentation);

        return $this->render('project_presentation/edit/places/manage.html.twig', [
            'presentation' => $presentation,
            'stringId' => $presentation->getStringId(),
        ]);
    }

    
    /** 
     * @Route("/projects/{stringId}/places/ajax-new-place", name="ajax_add_place") 
    */ 
    public function ajaxNewPlace(Request $request, PPBase $presentation, EntityManagerInterface $manager) {

        $this->denyAccessUnlessGranted('edit', $presentation);

        // getting data then hydrating a new place object

        if ($request->isXmlHttpRequest()) {

            $type = $request->request->get('type');
            $name = $request->request->get('name');
            $latitude = $request->request->get('latitude');
            $longitude = $request->request->get('longitude');
            $country = $request->request->get('country');
            $administrativeAreaLevel1 = $request->request->get('administrativeAreaLevel1');
            $administrativeAreaLevel2 = $request->request->get('administrativeAreaLevel2');
            $locality = $request->request->get('locality');
            $sublocalityLevel1 = $request->request->get('sublocalityLevel1');
            $postalCode = $request->request->get('postalCode');

            $newPlace = new Place();
            $newPlace
        
                -> setType($type)
                -> setName($name)
                -> setLatitude($latitude)
                -> setLongitude($longitude)
                -> setCountry($country)
                -> setAdministrativeAreaLevel1($administrativeAreaLevel1)
                -> setAdministrativeAreaLevel2($administrativeAreaLevel2)
                -> setLocality($locality)
                -> setSublocalityLevel1($sublocalityLevel1)
                -> setPostalCode($postalCode)
            ;

            //Avoid clones

            $currentPresentationPlaces = $presentation->getPlaces();

            
            if (!$currentPresentationPlaces->contains($newPlace)) {
                $presentation->addPlace($newPlace);
    
                $manager->persist($newPlace);
                $manager->persist($presentation);
                $manager->flush();

                $newPlaceId = $newPlace->getId();

                $feedbackCode = true;
            }

            $dataResponse = [
               
                'newPlaceId' =>  $newPlaceId,
                'feedbackCode' => $feedbackCode,
            ];

            return new JsonResponse($dataResponse);
            
        }

    }

    
    /**
     * Allow to remove a project place
     * 
     * @Route("/projects/{stringId}/places/ajax-remove-place/", name="ajax_remove_place")
     * 
     * @return Response
     */
   public function delete(Request $request, PPBase $presentation, EntityManagerInterface $manager, PlaceRepository $repo){

    $this->denyAccessUnlessGranted('edit', $presentation);

    if ($request->isXmlHttpRequest()) {

        $placeId = $request->request->get('placeId');

        $place = $repo->findOneById($placeId);

        $feedbackCode = false;

        if ($presentation->getPlaces()->contains($place)) {

            $presentation->removePlace($place);
            
            $manager->remove($place);
            
            $manager->flush();

            $feedbackCode = true;
        }

        $dataResponse = [
            'deletedPlaceId' =>  $placeId,
            'feedbackCode' => $feedbackCode,
        ];

        return new JsonResponse($dataResponse);

    }
}






}

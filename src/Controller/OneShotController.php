<?php

namespace App\Controller;

use App\Entity\PPBase;
use App\Service\CacheThumbnail;
use Doctrine\ORM\EntityManager;
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
    public function doAction(PPBaseRepository $repo, SearchService $searchService, EntityManagerInterface $manager, CacheThumbnail $cacheThumbnail): Response
    {

        $presentations = $repo->findAll();

        foreach ($presentations as $presentation) {
            /* $presentation->setCacheItem('thumbnailParentImageAddress', null);
            $presentation->setCacheItem('thumbnailAddress', null); */

            $cacheThumbnail->cacheThumbnail($presentation);

            /* $presentation->unsetCacheItem('thumbnail'); */
        }

        $manager->flush();

        /*   $em = $this->getDoctrine()->getManagerForClass(PPBase::class);

        foreach ($presentations as $presentation) {
            $searchService->index($em, $presentation);
        } */

        return $this->render('one_shot/index.html.twig', [
            'controller_name' => 'OneShotController',
        ]);

    }

}

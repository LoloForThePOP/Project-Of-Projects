<?php

namespace App\Controller;

use App\Entity\PPBase;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MiscController extends AbstractController
{
     
     /**
     * 
     * Render a chunk of an html page, thanks to an ajax call, in order to reduce page load.
     * 
     * @Route("/ajax-render-chunk", name="ajax_render_chunk")
     */
    public function ajaxRenderChunk(Request $request)
    {
                 
        if ($request->isXmlHttpRequest()) {

            session_write_close();

            $chunkName = $request->request->get('chunkName');

            //dump($chunkName);

            $additionalParameters = [];

            if ($request->request->get('params')) {

                $additionalParameters = $request->request->get('params');
            }

            //dump($additionalParameters);

            $chunkTemplateDirFileName = '';

            switch ($chunkName) {

                case 'plans':
                    $chunkTemplateDirFileName = '/plans/_details';
                    break;

                case 'edit_text_description':

                    $chunkTemplateDirFileName = '/project_presentation/_show/text_description/ajax';

                    if (isset($additionalParameters["idPP"])) {

                        $presentation = $this->getDoctrine()->getRepository(PPBase::class)->findOneById($additionalParameters["idPP"]);

                        if ($this->isGranted('edit', $presentation)) {

                            $additionalParameters["textDescription"] =  $presentation->getTextDescription();

                        }

                    }

                    break;


                default:
                
                    throw new \Exception('Invalid chunk name, passed '.$chunkName);
                    break;

            }

            $html = $this->renderView(
                    
                $chunkTemplateDirFileName.'.html.twig', 

                $additionalParameters
            );

            $htmlChunk = [

                "html" => $html

            ];

            //dump($htmlChunk);

            return new JsonResponse($htmlChunk);

        }

    }


    
    /**
     * 
     * Test something
     * 
     * @Route("/test-something", name="test_something")
     */
    public function test(): Response
    {

        return $this->render("/test_something.html.twig", [
            
        ]);

    }


     /**
     * @Route("/sitemap.xml", name="sitemap", defaults={"_format"="xml"})
     */
    public function index(Request $request, EntityManagerInterface $manager)
    {
        // Getting hostname
        $hostname = $request->getSchemeAndHttpHost();
        
        $sitemapPreparation =[];    // array with potential 'loc', 'title', 'updatedAt'... contents.
            
        // Static urls :

        $staticUrls=[];

        //From static controller :

        $staticControllerPageNames = ["terms", "short_manifesto", "about_us", "credits", "privacy"];


        //static pages urls generation

        foreach ($staticControllerPageNames as $value){

            $staticUrls[] = $this->generateUrl('static', ['page_name' => $value]);

        }


        //Other static pages urls generation : 

        $staticUrls[]=$this->generateUrl('homepage');

        $staticUrls[]=$this->generateUrl('contact_website');

        $staticUrls[]=$this->generateUrl('app_register');

        // sitemap attributes for static pages

        foreach ($staticUrls as $value){

            $sitemapPreparation[] = ['loc' => $value];

        }

        // Project presentations:

        $accessiblePresentations = $manager->createQuery('SELECT p FROM App\Entity\PPBase p WHERE p.isPublished=true AND p.overallQualityAssessment>=2 AND p.isAdminValidated=true ORDER BY p.createdAt DESC')->getResult();


        foreach ($accessiblePresentations as $accessiblePresentation) {


            $sitemapPreparation[] = [

            'loc' => $this->generateUrl('show_presentation', [

                                'stringId' => $accessiblePresentation->getStringId(),

                            ]),


            ];


        }

        $response = new Response(
            
            $this->renderView('sitemap.xml.twig', [
                'sitemapPreparation' => $sitemapPreparation,
                'hostname' => $hostname
                
            ]
            )
        );

        $response->headers->set('Content-Type', 'application/xml');

        return $response;

    }

}

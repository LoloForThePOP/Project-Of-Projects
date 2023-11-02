<?php

namespace App\Controller;

use OpenAI;
use App\Entity\PPBase;
use App\Entity\Category;
use App\Service\OpenAIAPI;
use App\Service\OpenAIService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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

        return new JsonResponse();

    }


    
    /**
     * 
     * Test something
     * 
     * @Route("/test-something", name="test_something")
     */
   /*   public function test()
    {

        //$ia = new OpenAIService ($_ENV['OPEN_AI_KEY']);

        //$answer = $ia->answer("I'm happy but...");
        
   
        return $this->render("/test_something.html.twig", [

        ]);

    } */


    /**
     * @Route("/upload-image", name="upload_image", methods={"POST"})
     */
    public function uploadImage(Request $request)
    {
        // Vérifiez si un fichier a été téléchargé
        $file = $request->files->get('file');

        if ($file) {
            // Générez un nom de fichier unique
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();

            // Déplacez le fichier téléchargé dans le répertoire d'upload
            $file->move(
                $this->getParameter('app.image_upload_directory'),
                $fileName
            );

            dump($this->getParameter('app.image_upload_directory'). $fileName);

            // Retournez une réponse JSON avec le chemin de l'image téléchargée
            return new Response(json_encode(['location' => $this->getParameter('app.image_upload_directory'). $fileName]));
        }

        // Si aucun fichier n'a été téléchargé, retournez une erreur
        return new Response('Erreur lors de l\'upload de l\'image', 400);
    }

     /**
     * @Route("/sitemap.xml", name="sitemap", defaults={"_format"="xml"})
     */
    public function sitemap(Request $request, EntityManagerInterface $manager)
    {
        // Getting hostname
        $hostname = $request->getSchemeAndHttpHost();
        
        $sitemapPreparation = [];    // array with potential 'loc', 'title', 'updatedAt'... contents.
            
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

        // Articles :

        $accessibleArticles = $manager->createQuery('SELECT a FROM App\Entity\Article a WHERE a.isValidated=true ORDER BY a.createdAt DESC')->getResult();

        foreach ($accessibleArticles as $accessibleArticle) {

            $sitemapPreparation[] = [

            'loc' => $this->generateUrl('show_article', [

                    'slug' => $accessibleArticle->getSlug(),

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

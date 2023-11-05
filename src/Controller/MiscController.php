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
     public function test()
    {

        //$ia = new OpenAIService ($_ENV['OPEN_AI_KEY']);

        //$answer = $ia->answer("I'm happy but...");

        $html = '<html><body>
    <img src="image1.jpg" alt="Image 1">
    <img src="image2.png" alt="Image 2">
    <img src="image3.gif" alt="Image 3">
</body></html>';

$matches = array();

// Utilisez une expression régulière pour extraire les noms de fichiers d'images depuis la balise "img" avec l'attribut "src"
$pattern = '/<img[^>]*src=["\']([^"\']+)["\'][^>]*>/i';

if (preg_match_all($pattern, $html, $matches)) {
    // Les noms de fichiers d'images extraits sont dans $matches[1]
    $imageFileNames = $matches[1];


    // Affichez les noms des fichiers d'images
    $result="";
    foreach ($imageFileNames as $fileName) {
        $result .= "Nom du fichier image : $fileName<br>";
    }
} else {
    $result = "Aucun fichier image trouvé dans la chaîne HTML.";
}
        
   
        return $this->render("/test_something.html.twig", [

            "result" => $result,

        ]);

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


    /**
     * @Route("/upload-image", name="upload_image", methods={"POST"})
    */
    public function uploadImage(Request $request)
    {
        $baseUrl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

        if (isset($_SERVER['HTTP_ORIGIN'])) {
            // same-origin requests won't set an origin. If the origin is set, it must be valid.
            if ($_SERVER['HTTP_ORIGIN'] == $baseUrl) {
                header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
            } else {
                $response = new JsonResponse(['message' => 'Origin Denied'], 403);
                return $response;
            }
        }

        // Don't attempt to process the upload on an OPTIONS request
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

            $response = new JsonResponse(['message' => 'Access-Control-Allow-Methods: POST, OPTIONS']);
            return $response;
        }


        $file = $request->files->get('file');

        //check if file has been dowloaded

        if ($file) {

            //Check file name : 
            $filename = $file->getClientOriginalName();
            if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $filename)) {
                $response = new JsonResponse(['message' => 'Invalid file name.'], 400);
                return $response;
            }

            // Check extension
            $extension = $file->getClientOriginalExtension();

            $allowedExtensions = ["gif", "jpg", "jpeg", "png", "webp"];
            $extension = $file->getClientOriginalExtension();
        
            if (!in_array(strtolower($extension), $allowedExtensions)) {
                $response = new JsonResponse(['message' => 'Invalid extension.'], 400);
                return $response;
            }

            // Check image size :

            $fileSizeInBytes = $file->getSize();
            $maxSizeInBytes = 1024 * 1024 * 7; //Max size 7 Mb.

            if ($fileSizeInBytes > $maxSizeInBytes) {

                $response = new JsonResponse(['message' => 'Le poids de l\'image est trop élevé.'], 400);

            }


            // Générez un nom de fichier unique
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();

            // Déplacez le fichier téléchargé dans le répertoire d'upload
            $file->move(
                $this->getParameter('app.image_upload_directory'),
                $fileName
            );

            // Retournez une réponse JSON avec le chemin de l'image téléchargée
            return new Response(json_encode(['location' => $baseUrl."/".$this->getParameter('app.image_upload_directory'). $fileName]));
        }

        // Si aucun fichier n'a été téléchargé, retournez une erreur
        return new Response('Erreur lors de l\'upload de l\'image', 400);
    }









}

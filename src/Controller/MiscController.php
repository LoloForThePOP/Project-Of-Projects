<?php

namespace App\Controller;

use OpenAI;
use App\Entity\User;
use App\Entity\PPBase;
use App\Entity\Category;
use App\Service\OpenAIAPI;
use App\Repository\UserRepository;
use App\Service\SessionVariablesService;
use Doctrine\ORM\EntityManager;
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

        /* UluleAPI $ulule,
        $ulule->fetchProjectInfo(); just testing Ulule api*/

        return $this->render("/test_something.html.twig", [

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

        $staticUrls[]=$this->generateUrl('ai_presentation_helper_origin');

        $staticUrls[]=$this->generateUrl('ai_presentation_helper_assistant_logo');

        $staticUrls[]=$this->generateUrl('ai_interview_helper_origin');

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

        // Articles:

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

        $session = $request->getSession();

        $imagesCount = $session->get('imagesCount'); // we don't want user to upload too much images in one session

        if ($imagesCount > 10) {
            $response = new JsonResponse(['message' => "Le nombre maximal de 10 images insérées pendant votre session est dépassé. Veuillez arrêter d'insérer des images ou vous reconnecter pour en insérer d'autres."]);
            return $response;
        }

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
            if (preg_match("/([^\w\s\d\~,;:\[\]\(\).À-ÿ6-8\-_])|([\.]{2,})/", $filename)) {
                $response = new JsonResponse(["message" => "Le nom du fichier comporte des caracteres non autorises."], 400);
                return $response;
            }

            // Check extension
            $extension = $file->getClientOriginalExtension();

            $allowedExtensions = ["gif", "jpg", "jpeg", "png", "webp"];
            $extension = $file->getClientOriginalExtension();
        
            if (!in_array(strtolower($extension), $allowedExtensions)) {
                $response = new JsonResponse(['message' => 'Les extensions de fichier acceptées sont "gif", "jpg", "jpeg", "png", "webp".'], 400);
                return $response;
            }

            // Check image size :

            $fileSizeInBytes = $file->getSize();
            $maxSizeInBytes = 1024 * 1024 * 7; // Max size 7 Mb.

            if ($fileSizeInBytes > $maxSizeInBytes) {

                $response = new JsonResponse(['message' => 'Le poids de l\'image est trop élevé.'], 400);

            }


            // Generate a unique file name
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();

            // Move file to appropriate upload folder
            $file->move(
                $this->getParameter('app.image_upload_directory'),
                $fileName
            );

            $session->set('imagesCount', $imagesCount++); // we increase images count to further check if max count is not exceeded

            // Return a json response with file location
            return new Response(json_encode(['location' => $baseUrl."/".$this->getParameter('app.image_upload_directory'). $fileName]));
        }

        // Si aucun fichier n'a été téléchargé, retournez une erreur
        return new Response('Erreur lors de l\'upload de l\'image', 400);
    }



     /**
     * @Route("/auth-redirections", name="auth_redirections")
    */
    public function authRedirections(SessionVariablesService $sessionVariables, EntityManagerInterface $em)
    {
        //Did user just create a presentation while not being logged in
        $fakeUserId = $sessionVariables->fakeUserId();

        if ($fakeUserId !== null) {

            $fakeUser = $this->getDoctrine()->getRepository(User::class)->findOneBy(['id' => $fakeUserId]);
            $fakeUserPresentation = $fakeUser->getCreatedPresentations()[0];
            $fakeUserPresentation->setDataItem("guest-presenter-activated", true);

            $this->getUser()->addCreatedPresentation($fakeUserPresentation);
            $fakeUser->removeCreatedPresentation($fakeUserPresentation);

            //dd($this->getUser());

            $em->flush();

            //supprimer la session;

            return $this->redirectToRoute('show_presentation', [
                'stringId' => $fakeUserPresentation->getStringId(),
            ]);

        }

        return $this->redirectToRoute('homepage');

    }






}

<?php

namespace App\Controller;


use App\Entity\User;
use App\Service\ImageService;
use App\Service\SessionVariablesService;
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
     * Render a chunk of an html page thanks to an ajax call in order to reduce page load.
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

                $additionalParameters = json_decode($request->request->get('params'), true);
            }

            $chunkTemplateDirFileName = '';

            switch ($chunkName) {

                case 'plans':
                    $chunkTemplateDirFileName = '/plans/_details';
                    break;

                case 'donation':
                    $chunkTemplateDirFileName = '/utilities/make_donation_form';
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

            return new JsonResponse($htmlChunk);

        }

        return new JsonResponse();

    }


    
    /**
     * 
     * This route allows to do some tests
     * 
     * @Route("/test-something", name="test_something")
     */
     public function test()
    {

        /* UluleAPI $ulule,
        $ulule->fetchProjectInfo(); just testing Ulule api*/

        return $this->render("/test_something.html.twig", [

        ]);

    }
    
    

     /**
      *
      * Allows to dynamically create a sitemap.xml file when sitemap route is called
      * 
      *
     * @Route("/sitemap.xml", name="sitemap", defaults={"_format"="xml"})
     */
    public function sitemap(Request $request, EntityManagerInterface $manager)
    {
        
        $sitemapPreparation = []; // Structured array that will be converted to xml format, with potential 'loc', 'title', 'updatedAt'...
            
        // Static urls management:

        $staticUrls=[]; //array that contains only urls from static pages

        // Static page file names

        $staticPageNames = ["terms", "short_manifesto", "about_us", "credits", "privacy"];

        // Static page urls generation

        foreach ($staticPageNames as $value){

            $staticUrls[] = $this->generateUrl('static', ['page_name' => $value]); //generated from staticController

        }

        // Other static pages urls generation not generated from staticController 

        $staticUrls[]=$this->generateUrl('homepage');

        $staticUrls[]=$this->generateUrl('contact_website');

        $staticUrls[]=$this->generateUrl('app_register');

        $staticUrls[]=$this->generateUrl('ai_presentation_helper_origin');

        $staticUrls[]=$this->generateUrl('ai_interview_helper_origin');

        // Injecting static pages urls into the sitemap preparation array

        foreach ($staticUrls as $value){

            $sitemapPreparation[] = ['loc' => $value];

        }

        // Adding project presentations to the sitemap

        // Getting public Project Presentation from DB

        $accessiblePresentations = $manager->createQuery('SELECT p FROM App\Entity\PPBase p WHERE p.isPublished=true AND p.overallQualityAssessment>=2 AND p.isAdminValidated=true ORDER BY p.createdAt DESC')->getResult();

        // Formatting these project presentation urls into the sitemap preparation array

        foreach ($accessiblePresentations as $accessiblePresentation) {

            $sitemapPreparation[] = [

            'loc' => $this->generateUrl('show_presentation', [

                    'stringId' => $accessiblePresentation->getStringId(),

                ]),

            ];

        }

        // Adding articles to the sitemap

        // Getting public articles from DB

        $accessibleArticles = $manager->createQuery('SELECT a FROM App\Entity\Article a WHERE a.isValidated=true ORDER BY a.createdAt DESC')->getResult();

        // Fromatting these article urls into the sitemap preparation array

        foreach ($accessibleArticles as $accessibleArticle) {

            $sitemapPreparation[] = [

            'loc' => $this->generateUrl('show_article', [

                    'slug' => $accessibleArticle->getSlug(),

                ]),

            ];

        }

        // Preparing & returning response
        $response = new Response(
                
                $this->renderView('sitemap.xml.twig', [
                    'sitemapPreparation' => $sitemapPreparation,
                    'hostname' => $request->getSchemeAndHttpHost()
                    
                ]
            )
        );

        $response->headers->set('Content-Type', 'application/xml');

        return $response;

    }


    /**
     * 
     * Allows user to upload images from tinyMCE rich text editor (ex: user wants to upload images for an article they are creating).
     * 
     * @Route("/upload-image", name="upload_image", methods={"POST"})
    */
    public function uploadImage(Request $request, ImageService $imageService)
    {

        $session = $request->getSession(); //to store & check if user has already downloaded a lot of images during their session.

        $imagesCount = $session->get('imagesCount'); // we don't want user to upload too much images in one session

        if ($imagesCount > 10) {
            $response = new JsonResponse(['message' => "Le nombre maximal de 10 images insérées pendant votre session est dépassé. Veuillez arrêter d'insérer des images ou vous reconnecter pour en insérer d'autres."]);
            return $response;
        }


        $baseUrl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

        // Check if the 'HTTP_ORIGIN' header is present in the request, which is used by browsers
        // to indicate the origin of the request in Cross-Origin Resource Sharing (CORS) scenarios.
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            // same-origin requests won't set an origin. If the origin is set, it must be valid.
            if ($_SERVER['HTTP_ORIGIN'] == $baseUrl) {
                header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
            } else {// If the origin doesn't match the base URL, it means the request is coming
                // from an untrusted domain. We deny access by returning a JSON response
                // with a message "Origin Denied" and an HTTP 403 status (Forbidden).
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

            //Checking file name : 
            $filename = $file->getClientOriginalName();
            if (preg_match("/([^\w\s\d\~,;:\[\]\(\).À-ÿ6-8\-_])|([\.]{2,})/", $filename)) {
                $response = new JsonResponse(["message" => "Le nom du fichier comporte des caracteres non autorises."], 400);
                return $response;
            }

            // Checking file extension
            $extension = $file->getClientOriginalExtension();

            $allowedExtensions = ["gif", "jpg", "jpeg", "png", "webp"];
            $extension = $file->getClientOriginalExtension();
        
            if (!in_array(strtolower($extension), $allowedExtensions)) {
                $response = new JsonResponse(['message' => 'Les extensions de fichier acceptées sont "gif", "jpg", "jpeg", "png", "webp".'], 400);
                return $response;
            }

            // Checking image size
            $fileSizeInBytes = $file->getSize();
            $maxSizeInBytes = 1024 * 1024 * 7; // Max size 7 Mb.

            if ($fileSizeInBytes > $maxSizeInBytes) {// case image weight is too large

                $response = new JsonResponse(['message' => 'Le poids de l\'image est trop élevé.'], 400);

            }

            // Generating a unique file name with extension
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();

            // Moving image file to appropriate upload folder
            $file->move(
                $this->getParameter('app.image_upload_directory'),
                $fileName
            );

            //CONDITIONAL TO DO: reduce image size using an image resizer service (we got one called ImageResizer). I have not done it now because as of 2024 - 10 - 15 just a few user have uploaded images for articles versus it could take a bit of coding time as for now the ImageResizer service only handles images that are attributes of entities (which is not the cas here, article images are not associated with article entities, they just have a specific path). Note that technically we could inject these articles images without entities into actual article Entities using (https://github.com/dustin10/VichUploaderBundle/blob/master/docs/other_usages/replacing_file.md). Would worth it if this route is used enough :)

            $session->set('imagesCount', $imagesCount++); // Increasing images count in a session to check if next time user upload an image the max count is exceeded or not

            // Returning a json response with server side file location
            return new Response(json_encode(['location' => $baseUrl."/".$this->getParameter('app.image_upload_directory'). $fileName]));
        }

        // Si aucun fichier n'a été téléchargé, retournez une erreur
        return new Response('Erreur lors de l\'upload de l\'image', 400);
    }



    /**
     * When a user authenticates to the app, we check if this user has just been an anymous user presenting a project as a guest. If true, we get the matching db guest user id stored in a session variable so that we give back to the authenticating user the work he / she has done as a guest.
     * 
     * @Route("/auth-redirections", name="auth_redirections")
    */
    public function authRedirections(SessionVariablesService $sessionVariables, EntityManagerInterface $em)
    {

        $guestUserId = $sessionVariables->guestUserId(); //checking if authenticating user has just been a guest user anonymously presenting a project (true if not null).

        if ($guestUserId !== null) {//if authenticating user have a guest user id stored in a session variable, it means that they just used the app as a guest and now he authenticates to the app.

            $guestUser = $this->getDoctrine()->getRepository(User::class)->findOneBy(['id' => $guestUserId]);//searching in db the guest user matching with the online anonymous user that is logging into the app.

            $guestUserPresentation = $guestUser->getCreatedPresentations()[0];// searching the db stored project presentation done by the guest user.
            
            $guestUserPresentation->setDataItem("guest-presenter-activated", true);//flagging that a solid user is claiming the work done as a guest

            $this->getUser()->addCreatedPresentation($guestUserPresentation);//Adding the presentation to the authenticating user
            $guestUser->removeCreatedPresentation($guestUserPresentation);//removing the presentation from the guest user sotred in db (we don't need this user anymore, he served as a storage)

            $em->flush();

            $sessionVariables->guestUserId(null, true); //delete the session variable we don't use anymore since user has logged in.

            return $this->redirectToRoute('show_presentation', [ //redirect the newly logged in user to the presentation page he has created.
                'stringId' => $guestUserPresentation->getStringId(),
            ]);

        }

        return $this->redirectToRoute('homepage');

    }

}

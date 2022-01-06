<?php

namespace App\Controller;

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

            $chunkName = $request->request->get('chunkName');
            $chunkTemplateDirFileName = '';

            switch ($chunkName) {

                case 'plans':
                    $chunkTemplateDirFileName = '/plans/_details';
                    break;
                
                default:
                
                    throw new \Exception('Invalid chunk name, passed '.$chunkName);
                    break;

            }

            $htmlChunk = [

                'html' => $this->renderView(
                    
                    $chunkTemplateDirFileName.'.html.twig', 

                    /* [
                        'parameter' => $parameter,
                    ] */
                ),
            ];

            //dump($dataResponse);

            return new JsonResponse($htmlChunk);

        }

    }



}

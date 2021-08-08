<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HelpController extends AbstractController
{


    /**
     * 
     * Ajax request contextual help, return appropriate html.
     * 
     * @Route("/help/ajax-request", name="ajax_contextual_help")
     * 
     */
    public function contextualHelp(Request $request)
    {
        
        if ($request->isXmlHttpRequest()) {

            //get selected news

            $context = $request->request->get('context');

            $dataResponse = [

                'html' => $this->renderView(
                    
                    'help/pp_wysiwyg/_'.$context.'.html.twig', 

                ),

            ];

            // dump($dataResponse);

            return new JsonResponse($dataResponse);

        
        }

    }



}

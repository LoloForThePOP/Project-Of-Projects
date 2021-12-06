<?php

namespace App\Controller;

use App\Form\BasicPoolType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PoolController extends AbstractController
{

    protected $storagePath = '../var/basic_website_feedback_pool.json';

    /** 
     * 
     * Allow to get pool form template
     *  
     * @Route("/get-pool-form-template", name="get_pool_form_template") 
     * 
     */
    public function getPoolFormTemplate(Request $request)
    {
        $basicPoolForm = $this->createForm(BasicPoolType::class,
        null,
        array(

            // Time protection
            'antispam_time'     => true,
            'antispam_time_min' => 4,
            'antispam_time_max' => 3600,
        ));

        $basicPoolForm->handleRequest($request);

        if ($basicPoolForm->isSubmitted() && $basicPoolForm->isValid()) {

            $myfile = file_put_contents($this->storagePath, json_encode($basicPoolForm->getViewData(), JSON_PRETTY_PRINT).PHP_EOL, FILE_APPEND | LOCK_EX);


            dd($basicPoolForm->getViewData());


            $this->addFlash(
                'success',
                "✅ Merci pour votre participation :-)"
            );

            return $this->redirectToRoute('homepage', [
                
            ]);
        }

        return $this->render('pools/_form.html.twig', [
            'basicPoolForm' => $basicPoolForm->createView(),
        ]);
        
    }

    /** 
     * 
     * Allow to display pool data
     *  
     * @Route("/admin/display-pool-data", name="display_pool_data") 
     * 
     */
    public function displayPoolData()
    {
        
        $dataSet = file_get_contents($this->storagePath);

        return $this->render('pools/display_data.html.twig', [
            'dataSet' => $dataSet,
        ]);
        
    }





}

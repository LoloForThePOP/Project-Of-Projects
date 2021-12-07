<?php

namespace App\Controller;

use App\Form\BasicPoolType;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
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
     * @Route("/get-pool-form-template", name="fill_basic_pool_form") 
     * 
     */
    public function getPoolFormTemplate(Request $request, MailerInterface $mailer)
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

            $result = $basicPoolForm->getViewData();

            $user=$this->getUser();

            if ($user) {
                $result['email'] = $user->getEmail();
            }

            file_put_contents($this->storagePath, json_encode($result, JSON_PRETTY_PRINT).PHP_EOL, FILE_APPEND | LOCK_EX);

            //alerting administrator with an email
            $sender = $this->getParameter('app.support_email');
            $receiver = $this->getParameter('app.support_email');

            $email = (new Email())
                ->from($sender)
                ->to($receiver)
                ->subject('New Pool Result')
                ->html('<pre>'.json_encode($result, JSON_PRETTY_PRINT).'</pre>');

            $mailer->send($email);


            $this->addFlash(
                'success',
                "✅ Merci pour votre participation 👍<br>Vos réponses seront prises en considération pour améliorer le site 👍"
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

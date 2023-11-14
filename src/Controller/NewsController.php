<?php

namespace App\Controller;

use App\Entity\News;
use App\Form\NewsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NewsController extends AbstractController
{
    #[Route('/news/edit/{id}', name: 'edit_news')]
    public function index(News $news, Request $request, EntityManagerInterface $manager): Response
    {
        $this->denyAccessUnlessGranted('edit', $news);

        $newsForm = $this->createForm(NewsType::class, $news);
        $newsForm->handleRequest($request);

        if ($newsForm->isSubmitted() && $newsForm->isValid()) {
            
            $manager->flush();

            return $this->redirectToRoute(
                'show_presentation',

                [

                    'stringId' => $news->getProject()->getStringId(),
                    '_fragment' => 'news-struct-container'

                ]

            );

        }

        return $this->render('news/edit.html.twig', [
            'newsForm' => $newsForm->createView(),
            'news' => $news,
            'ppStringId' => $news->getProject()->getStringId(),
        ]);

    }
}

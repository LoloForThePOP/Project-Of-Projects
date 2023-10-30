<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{

    
    #[Route('/articles', name: 'index_articles')]
    public function index(ArticleRepository $repo): Response
    {
        $articles = $repo->findAll();

        return $this->render('article/index.html.twig', [
            'articles' => $articles,
        ]);
    }
    

    #[Route('/articles/edit/{id?}', name: 'edit_article')]
    public function edit(ArticleRepository $repo, $id = null, Request $request, EntityManagerInterface $manager): Response
    {
        //$this->denyAccessUnlessGranted('edit', $pp);

        if($id != null){

            $article = $repo->findOneById($id);

        } else{

            $article = new Article ();

        }

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $manager->persist($article);
                             
            $manager->flush();

            $this->addFlash(
                'success',
                "✅ Article édité"
            );

            return $this->redirectToRoute('homepage', [

            ]);

        }

        return $this->render('article/edit.html.twig', [
            'form' => $form->createView(),
        ]);

    }

    
    #[Route('/articles/show/{id}', name: 'show_article')]
    public function show(Article $article): Response
    {

        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }





}

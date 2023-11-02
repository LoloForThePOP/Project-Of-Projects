<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
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
    public function edit(ArticleRepository $repo, $id = null, Request $request, EntityManagerInterface $manager, SluggerInterface $slugger, Security $security): Response
    {

        if($id != null){

            $article = $repo->findOneById($id);
                
            if (!$security->isGranted('user_edit', $article) && !$security->isGranted('admin_edit', $article) ) {

                throw $this->createAccessDeniedException();

            }
        }        
        
        else{

            $article = new Article ();
            $article->setAuthor($this->getUser());

        }

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            // If no slug we create one with article title
            if ($article->getSlug() === null || trim($article->getSlug()) === '') {

                $article->setSlug(strtolower($slugger->slug($article->getTitle())));

            }

            $manager->persist($article);
                             
            $manager->flush();

            $this->addFlash(
                'success',
                "✅ Article édité"
            );

            return $this->redirectToRoute('homepage', [

            ]);

        }

        return $this->render('/article/edit.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
        ]);

    }


    
    /**
     * 
     * Test something
     * 
     * @Route("test/something/{id?}", name="test_something")
     */
    public function test(ArticleRepository $repo, $id = null, Request $request, EntityManagerInterface $manager, SluggerInterface $slugger, Security $security): Response
    {

        if($id != null){

            $article = $repo->findOneById($id);
                
            if (!$security->isGranted('user_edit', $article) && !$security->isGranted('admin_edit', $article) ) {

                throw $this->createAccessDeniedException();

            }
        }        
        
        else{

            $article = new Article ();
            $article->setAuthor($this->getUser());

        }

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        
        if ($form->isSubmitted() && $form->isValid()){

            // If no slug we create one with article title
            if ($article->getSlug() === null || trim($article->getSlug()) === '') {

                $article->setSlug(strtolower($slugger->slug($article->getTitle())));

            }

            $manager->persist($article);
                             
            $manager->flush();

            $this->addFlash(
                'success',
                "✅ Article édité"
            );

            return $this->redirectToRoute('homepage', [

            ]);

        }

        //$ia = new OpenAIService ($_ENV['OPEN_AI_KEY']);

        //$answer = $ia->answer("I'm happy but...");
        
   
        return $this->render("/article/edit.html.twig", [

            'form' => $form->createView(),
            'article' => $article,

        ]);

    }
    
    #[Route('/articles/show/{slug}', name: 'show_article')]
    public function show(Article $article): Response
    {

        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    





}

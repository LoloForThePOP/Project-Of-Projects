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

            // We store initial content in order to check afterwards id some images have been deleted 
            $initialArticleContentHtml = $article->getContent();
                
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

            if($article->getId() != null){// article is not new, some images might have been deleted in the article, so we remove them on server.

                $afterSubmissionArticleContentHtml = $article->getContent();

                // Extract image names from both HTML strings
                $imageNames1 = $this->extractImageNames($initialArticleContentHtml);
                $imageNames2 = $this->extractImageNames($afterSubmissionArticleContentHtml);

                // Find deleted images (present in $imageNames1 but not in $imageNames2)
                $deletedImages = array_diff($imageNames1, $imageNames2);

                //delete the images :

                $deletedImagesDirectory = $this->getParameter('app.image_upload_directory'); // Directory where the images are stored

                foreach ($deletedImages as $imageName) {
                    // Construct the full path to the image file
                    $imageFilePath = $deletedImagesDirectory . '/' . $imageName;

                    // Check if the file exists before attempting to delete it
                    if (file_exists($imageFilePath)) {
                        // Delete the file
                        unlink($imageFilePath);
                    }

                }

            }


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

                'test' => $imageNames,

            ]);

        }

        return $this->render('/article/edit.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
        ]);

    }



    // Extract image names from an HTML string
    function extractImageNames($html) {
        $matches = array();
        $pattern = '/<img[^>]*src=["\']([^"\']+)["\'][^>]*>/i';
        
        if (preg_match_all($pattern, $html, $matches)) {
            return $matches[1];
        }
        
        return array();
    }
    


 
    #[Route('/articles/show/{slug}', name: 'show_article')]
    public function show(Article $article): Response
    {

        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    





}

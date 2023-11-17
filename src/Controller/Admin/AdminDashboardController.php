<?php

namespace App\Controller\Admin;

use App\Entity\News;
use App\Entity\User;
use App\Entity\PPBase;
use App\Entity\Article;
use App\Entity\Comment;
use App\Repository\ArticleRepository;
use App\Controller\Admin\PPBaseCrudController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class AdminDashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        // redirect to some CRUD controller
        $routeBuilder = $this->get(AdminUrlGenerator::class);

        //return $this->redirect($routeBuilder->setController(PPBaseCrudController::class)->generateUrl());


        $articles = $this->getDoctrine()->getRepository(Article::class)->findAll();
        $news = $this->getDoctrine()->getRepository(News::class)->findBy(array(),array('id'=>'DESC'),10,0);
        $comments = $this->getDoctrine()->getRepository(Comment::class)->findBy(array(),array('id'=>'DESC'),20,0);
        $pp = $this->getDoctrine()->getRepository(PPBase::class)->findBy(array(),array('id'=>'DESC'),10,0);

        return $this->render('admin/home.html.twig', [
            'articles' => $articles,
            'news' => $news,
            'comments' => $comments,
            'pp' => $pp,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()

        // the name visible to end users
        ->setTitle('<a href="/"><img src="/media/static/images/propon_logo.svg" style="width: 100px;"></a>');
    }

    
    public function configureAssets(): Assets
    {
        return Assets::new()->addCssFile('css/easy_admin.css');
    }

    public function configureMenuItems(): iterable
    
    {
        yield MenuItem::linkToCrud('Projets', 'fas fa-list', PPBase::class)->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-user', User::class)->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToLogout('Se déconnecter', 'fa fa-exit');
    }
}

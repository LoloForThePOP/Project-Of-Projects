<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\PPBase;
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

        return $this->render('admin/home.html.twig', [
            /* 'accounts' => $accounts,
            'contacts' => $contacts, */
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()

        // the name visible to end users
        ->setTitle('<a href="/">Propon</a>');
    }

    
    public function configureAssets(): Assets
    {
        return Assets::new()->addCssFile('css/easy_admin.css');
    }

    public function configureMenuItems(): iterable
    
    {
        yield MenuItem::linkToCrud('Projets', 'fas fa-list', PPBase::class);
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-user', User::class);
        yield MenuItem::linkToLogout('Se déconnecter', 'fa fa-exit');
    }
}

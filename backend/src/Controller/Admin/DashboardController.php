<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\City;
use App\Entity\Listing;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(private AdminUrlGenerator $urlGenerator)
    {
    }

    public function index(): Response
    {
        return $this->redirect(
            $this->urlGenerator->setController(ListingCrudController::class)->set('status', Listing::STATUS_PENDING)->generateUrl()
        );
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()->setTitle('EvoFind Beheer')->setTranslationDomain('admin')->setLocales(['nl']);
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Advertenties', 'fa fa-tags', Listing::class);
        yield MenuItem::linkToCrud('Categorieën', 'fa fa-folder', Category::class);
        yield MenuItem::linkToCrud('Steden', 'fa fa-city', City::class);
        yield MenuItem::linkToCrud('Gebruikers', 'fa fa-users', User::class);
        yield MenuItem::linkToRoute('Naar de website', 'fa fa-globe', 'app_home_redirect');
    }
}

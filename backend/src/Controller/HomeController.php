<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\City;
use App\Entity\Listing;
use App\Repository\ListingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home_redirect')]
    public function redirectToLocale(Request $request): RedirectResponse
    {
        $locale = $request->getPreferredLanguage(['nl', 'en', 'ar']) ?? 'nl';

        return $this->redirectToRoute('app_home', ['_locale' => $locale]);
    }

    #[Route('/{_locale}', name: 'app_home', requirements: ['_locale' => 'nl|en|ar'])]
    public function home(Request $request, ListingRepository $listings, EntityManagerInterface $em): Response
    {
        $category = $request->query->getInt('category') ?: null;
        $city = $request->query->getInt('city') ?: null;

        return $this->render('home.html.twig', [
            'listings' => $listings->findPublic(
                trim((string) $request->query->get('q')) ?: null,
                $category ? $em->getReference(Category::class, $category) : null,
                $city ? $em->getReference(City::class, $city) : null,
            ),
            'categories' => $em->getRepository(Category::class)->findAll(),
            'cities' => $em->getRepository(City::class)->findBy([], ['name' => 'ASC']),
        ]);
    }

    #[Route('/{_locale}/listings/{id}', name: 'app_listing_show', requirements: ['_locale' => 'nl|en|ar', 'id' => '\d+'])]
    public function show(Listing $listing): Response
    {
        $mine = $listing->getOwner() === $this->getUser();
        if (!$listing->isApproved() && !$mine && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createNotFoundException();
        }

        return $this->render('listing/show.html.twig', ['listing' => $listing]);
    }
}

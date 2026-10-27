<?php

namespace App\Controller;

use App\Entity\Listing;
use App\Entity\User;
use App\Form\ListingType;
use App\Repository\ListingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/{_locale}', requirements: ['_locale' => 'nl|en|ar'])]
class ListingController extends AbstractController
{
    public function __construct(private string $uploadDir)
    {
    }

    #[Route('/listings/new', name: 'app_listing_new', priority: 10)]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        \assert($user instanceof User);

        $listing = (new Listing())->setOwner($user);
        $form = $this->createForm(ListingType::class, $listing, ['locale' => $request->getLocale()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('imageFile')->getData();
            if ($file) {
                $name = bin2hex(random_bytes(16)).'.'.($file->guessExtension() ?: 'jpg');
                $file->move($this->uploadDir, $name);
                $listing->setImage($name);
            }
            // Every ad must be confirmed by an admin before it becomes visible.
            $listing->setStatus(Listing::STATUS_PENDING);
            $em->persist($listing);
            $em->flush();
            $this->addFlash('success', 'listing.submitted');

            return $this->redirectToRoute('app_my_listings');
        }

        return $this->render('listing/new.html.twig', ['form' => $form]);
    }

    #[Route('/my-listings', name: 'app_my_listings')]
    public function mine(ListingRepository $repo): Response
    {
        return $this->render('listing/mine.html.twig', [
            'listings' => $repo->findBy(['owner' => $this->getUser()], ['createdAt' => 'DESC']),
        ]);
    }
}

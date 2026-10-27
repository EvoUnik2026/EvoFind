<?php

namespace App\Controller\Admin;

use App\Entity\Listing;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

class ListingCrudController extends AbstractCrudController
{
    private const STATUS_LABELS = [
        'In afwachting' => Listing::STATUS_PENDING,
        'Goedgekeurd' => Listing::STATUS_APPROVED,
        'Afgewezen' => Listing::STATUS_REJECTED,
    ];

    public static function getEntityFqcn(): string
    {
        return Listing::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Advertentie')
            ->setEntityLabelInPlural('Advertenties')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add(ChoiceFilter::new('status', 'Status')->setChoices(self::STATUS_LABELS));
    }

    public function configureActions(Actions $actions): Actions
    {
        $approve = Action::new('approve', 'Goedkeuren', 'fa fa-check')
            ->linkToCrudAction('approve')
            ->displayIf(static fn (Listing $l): bool => !$l->isApproved())
            ->setCssClass('btn btn-success');
        $reject = Action::new('reject', 'Afwijzen', 'fa fa-ban')
            ->linkToCrudAction('reject')
            ->displayIf(static fn (Listing $l): bool => $l->getStatus() !== Listing::STATUS_REJECTED)
            ->setCssClass('btn btn-danger');

        return $actions
            ->disable(Action::NEW)
            ->add(Crud::PAGE_INDEX, $approve)
            ->add(Crud::PAGE_INDEX, $reject)
            ->add(Crud::PAGE_DETAIL, $approve)
            ->add(Crud::PAGE_DETAIL, $reject);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();
        yield TextField::new('title', 'Titel');
        yield TextareaField::new('description', 'Omschrijving')->hideOnIndex();
        yield MoneyField::new('priceCents', 'Prijs')->setCurrency('EUR')->setStoredAsCents();
        yield AssociationField::new('category', 'Categorie');
        yield AssociationField::new('city', 'Stad');
        yield AssociationField::new('owner', 'Aanbieder')->hideOnForm();
        yield ChoiceField::new('paymentOptions', 'Betaalopties')->setChoices(array_combine(Listing::PAYMENT_OPTIONS, Listing::PAYMENT_OPTIONS))->allowMultipleChoices()->hideOnIndex();
        yield ChoiceField::new('deliveryOptions', 'Bezorgopties')->setChoices(array_combine(Listing::DELIVERY_OPTIONS, Listing::DELIVERY_OPTIONS))->allowMultipleChoices()->hideOnIndex();
        yield ChoiceField::new('status', 'Status')->setChoices(self::STATUS_LABELS)
            ->renderAsBadges([
                Listing::STATUS_PENDING => 'warning',
                Listing::STATUS_APPROVED => 'success',
                Listing::STATUS_REJECTED => 'danger',
            ]);
        yield DateTimeField::new('createdAt', 'Aangemaakt')->hideOnForm();
    }

    public function approve(AdminContext $context, EntityManagerInterface $em, AdminUrlGenerator $urls): Response
    {
        return $this->setStatus($context, $em, $urls, Listing::STATUS_APPROVED);
    }

    public function reject(AdminContext $context, EntityManagerInterface $em, AdminUrlGenerator $urls): Response
    {
        return $this->setStatus($context, $em, $urls, Listing::STATUS_REJECTED);
    }

    private function setStatus(AdminContext $context, EntityManagerInterface $em, AdminUrlGenerator $urls, string $status): Response
    {
        $listing = $context->getEntity()->getInstance();
        \assert($listing instanceof Listing);
        $listing->setStatus($status);
        $em->flush();

        return $this->redirect($urls->setController(self::class)->setAction(Action::INDEX)->generateUrl());
    }
}

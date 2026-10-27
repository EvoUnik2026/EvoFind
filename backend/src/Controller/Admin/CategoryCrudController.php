<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInSingular('Categorie')->setEntityLabelInPlural('Categorieën');
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('slug', 'Code');
        yield TextField::new('nameNl', 'Naam (Nederlands)');
        yield TextField::new('nameEn', 'Naam (Engels)');
        yield TextField::new('nameAr', 'Naam (Arabisch)');
    }
}

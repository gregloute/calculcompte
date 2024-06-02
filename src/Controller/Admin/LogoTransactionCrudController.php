<?php

namespace App\Controller\Admin;

use App\Entity\LogoTransaction;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class LogoTransactionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return LogoTransaction::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('nom'),
            ImageField::new('fichier')->setLabel("Image")->setBasePath('img/sample/brand/')->setUploadDir('public/img/sample/brand/')->setRequired(false),
        ];
    }
}

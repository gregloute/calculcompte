<?php

namespace App\Controller\Admin;

use App\Entity\Utilisateur;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UtilisateurCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Utilisateur::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('username'),
            ChoiceField::new('roles')->setChoices([
                'Admin' => 'ROLE_ADMIN',
                'User' => 'ROLE_USER'
            ])->allowMultipleChoices()
        ];
    }
}

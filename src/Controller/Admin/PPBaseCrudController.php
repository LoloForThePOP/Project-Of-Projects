<?php

namespace App\Controller\Admin;

use App\Entity\PPBase;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class PPBaseCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PPBase::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}

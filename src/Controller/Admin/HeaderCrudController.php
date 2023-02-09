<?php

namespace App\Controller\Admin;

use App\Entity\Header;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class HeaderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Header::class;
    }

   
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', 'Titre du header'),
            TextareaField::new('content', 'Contenu'),
            TextField::new('btnTitle', 'Titre bouton'),
            TextField::new('btnUrl', 'Url bouton'),
            ImageField::new('illustration','Image Header')
            ->setBasePath('uploads/banner/')
            ->setUploadDir('public/uploads/banner')
            ->setFormType(FileUploadType::class)
            ->setUploadedFileNamePattern('[randomhash].[extension]')
            ->setRequired(false)
        ];
    }
    
}

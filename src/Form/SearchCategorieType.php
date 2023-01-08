<?php

namespace App\Form;

use App\Entity\Category;
use App\Classe\SearchCategorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SearchCategorieType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void
    { 
        $builder
            ->add('string', TextType::class,[
                'label' => false,
                'required' => false,
                 'attr' => [
                    'placeholder' => 'Je cherche...',
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('categorie', EntityType::class, [
                'label' => false,
                'class' => Category::class,
                'choice_label' => 'name',
                 'multiple' => true,
                 'expanded' => true
            ])
            ->add('Submit', SubmitType::class,[
                'label' => "Afficher les résultats",
                'attr' => [
                    'class' => 'btn-block btn-info  '
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchCategorie::class,
            'methode' => 'GET',
            'crsf_protection' => false
        ]);
    }

    //Pour ne pas afficher les variable envoyer sur URL
    public function getBlockPrefix()
    {
        return '';
    }
}
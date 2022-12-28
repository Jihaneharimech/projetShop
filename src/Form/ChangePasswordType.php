<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'disabled' => true,
                'label' =>'Email',
            ])    
            ->add('firstname', TextType::class, [
                'disabled' => true,
                'label' =>'Prénom',
            ])
            ->add('lastname', TextType::class, [
                'disabled' => true,
                'label' =>'Nom',
            ])
            ->add('old_password', PasswordType::class, [
               'label' =>'Mot de passe actual',
               'mapped' => false,
               'attr' => [
                'placeholder' => 'Veuillez saisir votre mot de passe actual'
               ]
            ])
            ->add('new_password', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'invalid_message' => 'Le nouveau mot d passe et la confirmation doivent identique',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Mot de passe',
                'attr' => [
                    'placeholder' => 'Veuillez saisir votre nouveau mot de passe '
                   ]
            ],
                'second_options' => ['label' => 'Confirmer votre nouveau mot de passe',
                'attr' => [
                    'placeholder' => 'Merci de confirmer votre nouveau mot de passe '
                   ]
            ],
            ])
            ->add('Submit', SubmitType::class,[
                'label' => "Envoyer"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username',TextType::class, [
                'required' => true,
                'label' => 'Username',
                'label_attr' => ['class' => 'label'],
                'row_attr' => [
                    'class' => 'form-group basic',
                ]
            ])
            ->add('password', PasswordType::class, [
                'required' => true,
                'label' => 'Password',
                'label_attr' => ['class' => 'label'],
                'row_attr' => [
                    'class' => 'form-group basic',
                ]
            ])
            ->add('passwordConfirm', PasswordType::class, [
                'required' => true,
                'label' => 'Password confirm',
                'label_attr' => ['class' => 'label'],
                'row_attr' => [
                    'class' => 'form-group basic',
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Create',
                'attr' => ['class' => 'btn btn-primary btn-block btn-lg mt-3'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
            'translation_domain' => 'forms',
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\TransactionSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransactionSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('motsName', TextType::class, [
                'required' => false,
                'label' => 'Name',
                'label_attr' => ['class' => 'label'],
                'row_attr' => [
                    'class' => 'form-group basic',
                ],
                'attr' => [
                    'placeholder' => 'Amazon'
                ]
            ])
            ->add('price', NumberType::class, [
                'required' => false,
                'label' => 'Value',
                'label_attr' => ['class' => 'label'],
                'row_attr' => [
                    'class' => 'form-group basic',
                ],
                'attr' => [
                    'placeholder' => '0,00'
                ]
            ])
            ->add('depense',CheckboxType::class, [
                'required' => false,
            ])
            ->add('revenu',CheckboxType::class, [
                'required' => false,
            ])
            ->add('recherche', SubmitType::class, [
                'label' => 'Search',
                'attr' => ['class' => 'btn btn-primary btn-block btn-lg '],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TransactionSearch::class,
            'translation_domain' => 'forms',
            'method' => 'get',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}

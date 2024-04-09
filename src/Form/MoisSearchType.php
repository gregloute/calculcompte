<?php

namespace App\Form;

use App\Entity\MoisSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MoisSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('motsName', TextType::class, [
                'required' => false,
                'label' => 'Name',
                'label_attr' => ['class' => 'label'],
                'attr' => [
                    'placeholder' => 'Name'
                ],
                'row_attr' => [
                    'class' => 'form-group basic',
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Search',
                'attr' => ['class' => 'btn btn-primary btn-block btn-lg mt-2'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MoisSearch::class,
            'translation_domain' => 'forms',
            'method' => 'get',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}

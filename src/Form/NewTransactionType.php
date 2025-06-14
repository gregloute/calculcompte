<?php

namespace App\Form;

use App\Entity\LogoTransaction;
use App\Entity\Transaction;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class
NewTransactionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'required' => true,
                'label' => 'Name',
                'label_attr' => ['class' => 'label'],
                'row_attr' => [
                    'class' => 'form-group basic',
                ]
            ])
            ->add('valeur', NumberType::class, [
                'required' => true,
                'label' => 'Value',
                'label_attr' => ['class' => 'label'],
                'row_attr' => [
                    'class' => 'form-group basic',
                ],
                'attr' => [
                    'value' => ''
                ]
            ])
            ->add('logo', EntityType::class, [
                'class' => LogoTransaction::class,
                'choice_label' => 'nom',
                'attr' => ['class' => 'form-control custom-select'],
                'row_attr' => ['class' => 'input-wrapper'],
                'label_attr' => ['class' => 'label']
            ])
            ->add('depense')
            ->add('surcompte')
            ->add('recurrent')
            ->add('endAt', TextType::class, [
                'required' => false,
                'row_attr' => ['id' => 'endAt']
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Add',
                'attr' => ['class' => 'btn btn-primary btn-block btn-lg '],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
            'translation_domain' => 'forms',
        ]);
    }
}

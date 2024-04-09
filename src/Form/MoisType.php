<?php

namespace App\Form;

use App\Entity\Mois;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MoisType extends AbstractType
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
            ->add('submit', SubmitType::class, [
                'label' => 'Add',
                'attr' => ['class' => 'btn btn-primary btn-block btn-lg mt-3'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Mois::class,
            'translation_domain' => 'forms',
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Aviso;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class AvisoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titulo', TextType::class, [
                'label' => 'Título',
                'empty_data' => '',
                'attr' => ['placeholder' => 'Corte de agua el jueves'],
            ])
            ->add('texto', TextareaType::class, [
                'label' => 'Texto del aviso',
                'empty_data' => '',
                'attr' => ['rows' => 5],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Aviso::class,
        ]);
    }
}
<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class PinType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pin', TextType::class, [
                'label' => 'PIN de la comunidad',
                'help' => 'Lo encontrarás en la nota del portal o te lo dará el administrador.',
                'constraints' => [new NotBlank(message: 'Escribe el PIN.')],
                'attr' => ['maxlength' => 6, 'autocomplete' => 'off'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
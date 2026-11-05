<?php

namespace App\Form;

use App\Entity\Comunidad;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ComunidadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, [
                'label' => 'Nombre de la comunidad',
                'empty_data' => '',
                'attr' => ['placeholder' => 'Comunidad Copacabana'],
            ])
            ->add('direccion', TextType::class, [
                'label' => 'Dirección',
                'empty_data' => '',
                'attr' => ['placeholder' => 'Rúa do Mar, 12, Marín'],
            ])
            ->add('pin', TextType::class, [
                'label' => 'PIN de la comunidad',
                'empty_data' => '',
                'help' => 'Código que usarán los vecinos para entrar en el tablón.',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comunidad::class,
        ]);
    }
}
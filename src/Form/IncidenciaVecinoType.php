<?php

namespace App\Form;

use App\Entity\Incidencia;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IncidenciaVecinoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('descripcion', TextareaType::class, [
                'label' => '¿Qué ocurre?',
                'empty_data' => '',
                'attr' => ['rows' => 4, 'placeholder' => 'Por ejemplo: la luz del portal no se enciende'],
            ])
            ->add('piso', TextType::class, [
                'label' => 'Piso (opcional)',
                'required' => false,
            ])
            ->add('telefono', TelType::class, [
                'label' => 'Teléfono (opcional)',
                'required' => false,
                'help' => 'Solo lo verá el administrador, por si necesita contactar contigo.',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Incidencia::class,
        ]);
    }
}
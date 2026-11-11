<?php

namespace App\Form;

use App\Entity\Incidencia;
use App\Enum\EstadoIncidencia;
use App\Enum\PrioridadIncidencia;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IncidenciaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('estado', EnumType::class, [
                'class' => EstadoIncidencia::class,
                'choice_label' => fn($estado) => $estado->etiqueta(),
            ])
            ->add('prioridad', EnumType::class, [
                'class' => PrioridadIncidencia::class,
                'choice_label' => fn($prioridad) => $prioridad->etiqueta(),
                'required' => false,
                'placeholder' => 'Sin prioridad',
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

<?php

namespace App\Form;

use App\Entity\Zona;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ZonaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, [
                'label' => 'Nombre de la zona',
                'empty_data' => '',
                'attr' => ['placeholder' => 'Ascensor, Portal, Garaje...'],
            ])
            ->add('tieneQr', CheckboxType::class, [
                'label' => 'Esta zona tiene código QR',
                'required' => false,
                'help' => 'Desmárcalo en zonas como el tejado o la fachada, donde no se pega ningún código.',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Zona::class,
        ]);
    }
}
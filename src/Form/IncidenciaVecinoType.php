<?php

namespace App\Form;

use App\Entity\Comunidad;
use App\Entity\Incidencia;
use App\Entity\Zona;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\Image;

class IncidenciaVecinoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['comunidad']) {
            $builder->add('zona', EntityType::class, [
                'class' => Zona::class,
                'choices' => $options['comunidad']->getZonas(),
                'choice_label' => 'nombre',
                'label' => 'Zona',
                'placeholder' => 'Elige la zona',
                'constraints' => [new NotBlank(message: 'Elige la zona.')],
            ]);
        }

        $builder
            ->add('descripcion', TextareaType::class, [
                'label' => '¿Qué ocurre?',
                'empty_data' => '',
                'attr' => ['rows' => 4],
            ])
            ->add('foto', FileType::class, [
                'label' => 'Foto (opcional)',
                'mapped' => false,
                'required' => false,
                'constraints' => [new Image()],
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
            'comunidad' => null,
        ]);

        $resolver->setAllowedTypes('comunidad', ['null', Comunidad::class]);
    }
}

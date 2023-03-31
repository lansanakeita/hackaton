<?php

namespace App\Form;

use App\Entity\Bookings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('start', DateType::class, array(
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'data' => new \DateTime()
            ))
            ->add('end', DateType::class, array(
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                //'data' => new \DateTime(),
                'required' => false
            ))
            ->add('slots', ChoiceType::class, [
                'placeholder' => 'Sélectionnez votre créneau',
                'choices'  => [
                    'Matinée'                     => 'Matinée',
                    'Après-midi'                  => 'Après-midi',
                    'Journée'                     => 'Journée',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Bookings::class,
        ]);
    }
}

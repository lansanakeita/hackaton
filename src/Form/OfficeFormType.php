<?php

namespace App\Form;

use App\Entity\Equipments;
use App\Entity\Kitchens;
use App\Entity\Office;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;

class OfficeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('roomSize', null, [
                'attr' => [
                    'placeholder' => 'Taille de la pièce',
                ],
            ])
            ->add('deskSize', null, [
                'attr' => [
                    'placeholder' => 'Taille du bureau',
                ],
            ])
            ->add('canLeaveBelongings')
            ->add('brightness', ChoiceType::class, [
                'choices' => [
                    'Forte' => 'Forte',
                    'Moyenne' => 'Moyenne',
                    'Faible' => 'Faible',
                ],
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('networkQuality', ChoiceType::class, [
                'choices' => [
                    'Excellente' => 'Excellente',
                    'Moyenne' => 'Moyenne',
                    'Bonne' => 'Bonne',
                ],
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('capacity', null, [
                'attr' => [
                    'placeholder' => 'Capacité d\'accueil',
                ],
            ])
            ->add('internetType', ChoiceType::class, [
                'choices' => [
                    'Fibre THD' => 'Fibre THD',
                    'ADSL' => 'ADSL',
                ],
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('description', null, [
                'attr' => [
                    'placeholder' => 'Description de l\'annonce',
                ],
            ])
            ->add('title', null, [
                'attr' => [
                    'placeholder' => 'Titre de l\'annonce',
                ],
            ])
            ->add('price', null, [
                'attr' => [
                    'placeholder' => 'Montant de la location',
                ],
            ])
            ->add('equipments', EntityType::class, [
                'class' => Equipments::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => false,
            ])
            ->add('kitchens', EntityType::class, [
                'class' => Kitchens::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => false,
            ])
            ->add('images', FileType::class, [
                'label' => 'Images (JPEG, PNG, GIF)',
                'multiple' => true,
                'mapped' => false, // ne pas mapper à une propriété de l'entité Office
                'required' => false, // les images ne sont pas obligatoires
                'constraints' => [
                    new Count([
                        'min' => 2,
                        'minMessage' => 'Veuillez télécharger au moins deux images.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Office::class,
        ]);
    }
}

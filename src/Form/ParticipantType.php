<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('roles')
            ->add('password')
            ->add('nom')
            ->add('prenom')
            ->add('actif')
            ->add('telephone')
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'id',
            ])
            ->add('mesInscriptions', EntityType::class, [
                'class' => Sortie::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('image', FileType::class, [
                'label'=>'Photo de profil',
                'mapped' =>false,
                'required' => false,
                'constraints' =>[
                   new Image(
                       maxSize: '5M',
                       maxSizeMessage: "Format de l'image trop gros !",
                       mimeTypesMessage: 'Format de l\'image non valide',

                   )]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}

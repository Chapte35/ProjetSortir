<?php

namespace App\Form\Filter;

use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use Spiriit\Bundle\FormFilterBundle\Filter\Form\Type\CheckboxFilterType;
use Spiriit\Bundle\FormFilterBundle\Filter\Form\Type\ChoiceFilterType;
use Spiriit\Bundle\FormFilterBundle\Filter\Form\Type\DateTimeFilterType;
use Spiriit\Bundle\FormFilterBundle\Filter\Form\Type\DateTimeRangeFilterType;
use Spiriit\Bundle\FormFilterBundle\Filter\Form\Type\EntityFilterType;
use Spiriit\Bundle\FormFilterBundle\Filter\Form\Type\TextFilterType;
use Spiriit\Bundle\FormFilterBundle\Filter\Query\QueryInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
//            ->add('site', EntityFilterType::class)
            ->add('site', EntityFilterType::class,[
                'class' => Site::class,
                'choice_label' => 'nom',
                'placeholder' => 'Site :',
                'required' => false,
            ])
            ->add('nom', TextFilterType::class,[
                'condition_pattern' => 4,
            ])

            ->add('dateHeureDebut', DateType::class,[
                'label' => 'Sortie comprise à partir de :',
                'widget' => 'single_text',
//                'time_widget' => 'single_text',
                'required' => false
            ])
            ->add('dateHeureFin', DateType::class,[
                'label' => 'Sorties comprises jusqu\'au',
                'widget' => 'single_text',
//                'time_widget' => 'single_text',
                'required' => false
            ])
            ->add('moiOrganisateur', CheckboxType::class,[
                'label' => 'Sortie dont je suis l\'organisateur',
                'required' => false
            ])
            ->add('moiInscrit', CheckboxType::class,[
                'label' => 'Sorties auxquelles je suis inscrit(e)',
                'required' => false
            ])
            ->add('moiPasInscrit', CheckboxType::class,[
                'label' => 'Sorties auxquelles je ne suis pas inscrit(e)',
                'required' => false
            ])
            ->add('sortiesPassees', CheckboxType::class,[
                'label' => 'Sorties Passées',
                'required' => false
            ])

//            ->add('organisateur', EntityFilterType::class,[
//                'class' => Participant::class,
//                'choice_label' => 'pseudo',
//                'placeholder' => 'Organisée par',
//                'required' => false,
//            ])
//            ->add('participants', EntityFilterType::class,[
//                'class' => Participant::class,
//                'choice_label' => 'pseudo',
//                'placeholder' => 'Participe à l\'évènement :',
//                'required' => false,
//            ])
//            ->add('etat', EntityFilterType::class,[
//                'class' => Etat::class,
//                'choice_label' => 'libelle',
//                'placeholder' => 'Etat :',
//                'required' => false,
//
//            ])
//            ->add('inscrit', CheckboxType::class, [
//                'label' => 'Sorties auxquelles je suis inscrit',
//                'required' => false,
//
//            ])
//            ->add('nonInscrit', CheckboxType::class, [
//                'label' => 'Sorties auxquelles je ne suis pas inscrit',
//                'required' => false,
//
//            ])
//            ->add('sortiesPassees', CheckboxType::class, [
//                'label' => 'Sorties archivées',
//                'required' => false,
//
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }

}

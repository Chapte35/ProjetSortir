<?php

namespace App\Form;

use App\Entity\Etat;
use App\Entity\GroupePrive;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Repository\GroupePriveRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortiesType extends AbstractType
{
    private Security $security;

    /**
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('dateHeureDebut', null, [
                'widget' => 'single_text',
            ])
            ->add('dureeMinutes', IntegerType::class,[
                'mapped' => false,
                ])
            ->add('dateLimiteInscription', null, [
                'widget' => 'single_text',
            ])
            ->add('nbInscriptionsMax')
            ->add('infosSortie')

            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'nom',
            ])
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'nom',
            ])
            ->add('groupe', EntityType::class, [
                'class' => GroupePrive::class,
                'query_builder' => function (GroupePriveRepository $gr): QueryBuilder {
                    return $gr->createQueryBuilder('g')
                        ->where('g.proprio = :user')
                        ->setParameter('user', $this->security->getUser())
                        ->orderBy('g.nom', 'ASC');
                },
                'choice_label' => 'nom',
                'mapped' => false,
                'placeholder' => 'Groupe prive (Laissez vide si vous voulez que la sortie soit publique) :',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);

    }
}

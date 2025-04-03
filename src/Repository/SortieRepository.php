<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Spiriit\Bundle\FormFilterBundle\Filter\FilterBuilderUpdater;

/**
 * @extends ServiceEntityRepository<Sortie>
 */
class SortieRepository extends ServiceEntityRepository
{
    private FilterBuilderUpdater $filterBuilderUpdater;
    private EntityManagerInterface $em;

    public function __construct(ManagerRegistry $registry, FilterBuilderUpdater $filterBuilderUpdater, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->filterBuilderUpdater = $filterBuilderUpdater;
        parent::__construct($registry, Sortie::class);
    }

    //    /**
    //     * @return Sortie[] Returns an array of Sortie objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Sortie
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }


    public function findAllJoin()
    {
        $builder = $this->em
            ->getRepository(Sortie::class)
            ->createQueryBuilder('s')
            ->addSelect(['site', 'org', 'part', 'etat'])
            ->leftJoin('s.site', 'site')
            ->leftJoin('s.organisateur', 'org')
            ->leftJoin('s.participants', 'part')
            ->leftJoin('s.etat', 'etat')
            ->orderBy('s.dateHeureDebut', 'ASC');

        return $builder->getQuery()->getResult();
    }


    /**
     * @throws \DateInvalidOperationException
     */

    public function getSortiesAVenir(){
        $builder = $this->em
            ->getRepository(Sortie::class)
            ->createQueryBuilder('sortie')
            ->addSelect(['site', 'org', 'part', 'etat'])
            ->leftJoin('sortie.site', 'site')
            ->leftJoin('sortie.organisateur', 'org')
            ->leftJoin('sortie.participants', 'part')
            ->leftJoin('sortie.etat', 'etat')
            ->andWhere('sortie.dateHeureDebut NOT BETWEEN :threeYearsBefore AND :now ')
            ->setParameter('threeYearsBefore',(new \DateTime())->sub(new \DateInterval('P3Y')))
            ->setParameter('now',new \DateTime())
            ->orderBy('s.dateHeureDebut', 'ASC');

            return $builder->getQuery()->getResult();

    }


    public function rechercheSorties($filters, $user)
    {

        $filterBuiler = $this->em
            ->getRepository(Sortie::class)
            ->createQueryBuilder('sortie')
            ->addSelect(['site', 'org', 'part', 'etat'])
            ->leftJoin('sortie.site', 'site')
            ->leftJoin('sortie.organisateur', 'org')
            ->leftJoin('sortie.participants', 'part')
            ->leftJoin('sortie.etat', 'etat');

        $nom = $filters->getData()['nom'];
        $site = $filters->getData()['site'];
        $dateHeureDebut = $filters->getData()['dateHeureDebut'];
        $dateHeureFin = $filters->getData()['dateHeureFin'];
        $moiOrganisateur = $filters->getData()['moiOrganisateur'];
        $moiInscrit = $filters->getData()['moiInscrit'];
        $moiPasInscrit = $filters->getData()['moiPasInscrit'];
        $sortiesPassees = $filters->getData()['sortiesPassees'];


//        dd($filters->getData()['nom']);


        if ($nom) {
            $filterBuiler->andWhere('sortie.nom LIKE :nom')
                ->setParameter('nom', "%" . $nom . "%");
        }

        if ($site) {
            $filterBuiler->andWhere('sortie.site = :site')
                ->setParameter('site', $site);
        }

        if ($dateHeureDebut && $dateHeureFin) {
            $filterBuiler->andWhere('sortie.dateHeureDebut BETWEEN :dateDebut AND :dateFin')
                ->setParameter('dateDebut', $dateHeureDebut)
                ->setParameter('dateFin', $dateHeureFin);
        }
        if ($moiOrganisateur) {
            $filterBuiler->andWhere('sortie.organisateur = :user')
                ->setParameter("user", $user);
        }

        if ($moiInscrit) {
            $filterBuiler->andWhere(':user MEMBER OF sortie.participants')
                ->setParameter("user", $user);
        }
        if ($moiPasInscrit) {
            $filterBuiler->andWhere(':user NOT MEMBER OF sortie.participants')
                ->setParameter("user", $user);
        }

        if ($sortiesPassees) {
            $filterBuiler->andWhere('sortie.dateHeureDebut BETWEEN :threeYearsAgo AND :now ')
                ->setParameter('threeYearsAgo', (new \DateTime())->sub(new \DateInterval('P3Y')))
                ->setParameter('now', new \DateTime());
        } else {
            $filterBuiler->andWhere('sortie.dateHeureDebut NOT BETWEEN :threeYearsAgo AND :now ')
                ->setParameter('threeYearsAgo', (new \DateTime())->sub(new \DateInterval('P3Y')))
                ->setParameter('now', new \DateTime());
        }

        $filterBuiler
            ->andWhere('etat.libelle NOT LIKE :excludedStates')
            ->setParameter('excludedStates', 'Archivée');


        return $filterBuiler->orderBy('sortie.dateHeureDebut', 'ASC')
            ->getQuery()
            ->getResult();
    }


    public function findActiveSorties()
    {
        return $this->createQueryBuilder('s')
            ->addSelect(['site', 'org', 'part', 'etat'])
            ->leftJoin('s.site', 'site')
            ->leftJoin('s.organisateur', 'org')
            ->leftJoin('s.participants', 'part')
            ->leftJoin('s.etat', 'etat')
            ->where('etat.libelle NOT IN (:excludedStates)')
            ->setParameter('excludedStates', ['Archivée'])
            ->andWhere('s.dateHeureDebut NOT BETWEEN :threeYearsBefore AND :now ')
            ->setParameter('threeYearsBefore', (new \DateTime())->sub(new \DateInterval('P3Y')))
            ->setParameter('now', new \DateTime())
            ->orderBy('s.dateHeureDebut', 'ASC')
            ->getQuery()
            ->getResult();
    }


    public function findnotAnnulee()
    {
        return
            $this->createQueryBuilder('s')->join('s.etat', 'e')
                ->where('e.libelle NOT IN (:excludedStates)')
                ->setParameter('excludedStates', ['Annulée', 'Archivée', 'Passée', 'Activite en Cours'])
                ->getQuery()
                ->getResult();

    }
}

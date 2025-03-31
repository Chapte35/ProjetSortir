<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
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

    public function rechercheSorties($qb, $filters, $user)
    {


        if (!empty($filters['inscrit'])) {
            $qb->andWhere(':user MEMBER OF sortie.Participants')
                ->setParameter('user', $user);
        }

        // Sorties auxquelles l'utilisateur n'est pas inscrit
        if (!empty($filters['nonInscrit'])) {
            $qb->andWhere(':user NOT MEMBER OF sortie.Participants')
                ->setParameter('user', $user);
        }

        // Sorties passées (jusqu'à un mois après la date de début)
        if (!empty($filters['sortiesPassees'])) {
            $oneMonthAgo = new \DateTime();
            $oneMonthAgo->sub(new \DateInterval('P1M'));

            $qb->andWhere('sortie.dateHeureDebut BETWEEN :oneMonthAgo AND :now')
                ->setParameter('oneMonthAgo', $oneMonthAgo)
                ->setParameter('now', new \DateTime());
        }


        return $qb->orderBy('sortie.dateHeureDebut', 'ASC')
            ->getQuery()
            ->getResult();
    }

}

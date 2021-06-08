<?php

namespace App\Repository;

use App\Entity\PPBase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PPBase|null find($id, $lockMode = null, $lockVersion = null)
 * @method PPBase|null findOneBy(array $criteria, array $orderBy = null)
 * @method PPBase[]    findAll()
 * @method PPBase[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PPBaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PPBase::class);
    }

    // /**
    //  * @return PPBase[] Returns an array of PPBase objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PPBase
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

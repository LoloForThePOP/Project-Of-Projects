<?php

namespace App\Repository;

use App\Entity\Persorg;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Persorg|null find($id, $lockMode = null, $lockVersion = null)
 * @method Persorg|null findOneBy(array $criteria, array $orderBy = null)
 * @method Persorg[]    findAll()
 * @method Persorg[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersorgRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Persorg::class);
    }

    // /**
    //  * @return Persorg[] Returns an array of Persorg objects
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
    public function findOneBySomeField($value): ?Persorg
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

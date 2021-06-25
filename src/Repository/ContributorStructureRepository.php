<?php

namespace App\Repository;

use App\Entity\ContributorStructure;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ContributorStructure|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContributorStructure|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContributorStructure[]    findAll()
 * @method ContributorStructure[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContributorStructureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContributorStructure::class);
    }

    // /**
    //  * @return ContributorStructure[] Returns an array of ContributorStructure objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ContributorStructure
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

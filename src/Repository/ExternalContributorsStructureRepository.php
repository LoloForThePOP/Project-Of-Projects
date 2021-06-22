<?php

namespace App\Repository;

use App\Entity\ExternalContributorsStructure;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ExternalContributorsStructure|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExternalContributorsStructure|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExternalContributorsStructure[]    findAll()
 * @method ExternalContributorsStructure[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExternalContributorsStructureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExternalContributorsStructure::class);
    }

    // /**
    //  * @return ExternalContributorsStructure[] Returns an array of ExternalContributorsStructure objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ExternalContributorsStructure
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

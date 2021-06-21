<?php

namespace App\Repository;

use App\Entity\ImageUpload;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ImageUpload|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImageUpload|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImageUpload[]    findAll()
 * @method ImageUpload[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageUploadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImageUpload::class);
    }

    // /**
    //  * @return ImageUpload[] Returns an array of ImageUpload objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ImageUpload
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

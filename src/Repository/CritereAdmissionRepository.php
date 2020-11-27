<?php

namespace App\Repository;

use App\Entity\CritereAdmission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CritereAdmission|null find($id, $lockMode = null, $lockVersion = null)
 * @method CritereAdmission|null findOneBy(array $criteria, array $orderBy = null)
 * @method CritereAdmission[]    findAll()
 * @method CritereAdmission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CritereAdmissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CritereAdmission::class);
    }

    // /**
    //  * @return CritereAdmission[] Returns an array of CritereAdmission objects
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
    public function findOneBySomeField($value): ?CritereAdmission
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

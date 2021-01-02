<?php

namespace App\Repository;

use App\Entity\Fabrique;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Fabrique|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fabrique|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fabrique[]    findAll()
 * @method Fabrique[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FabriqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fabrique::class);
    }

    // /**
    //  * @return Fabrique[] Returns an array of Fabrique objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Fabrique
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

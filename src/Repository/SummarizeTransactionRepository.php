<?php

namespace App\Repository;

use App\Entity\SummarizeTransaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SummarizeTransaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method SummarizeTransaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method SummarizeTransaction[]    findAll()
 * @method SummarizeTransaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SummarizeTransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SummarizeTransaction::class);
    }

    // /**
    //  * @return SummarizeTransaction[] Returns an array of SummarizeTransaction objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SummarizeTransaction
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

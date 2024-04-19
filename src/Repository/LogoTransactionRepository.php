<?php

namespace App\Repository;

use App\Entity\LogoTransaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LogoTransaction>
 *
 * @method LogoTransaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method LogoTransaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method LogoTransaction[]    findAll()
 * @method LogoTransaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogoTransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogoTransaction::class);
    }

//    /**
//     * @return LogoTransaction[] Returns an array of LogoTransaction objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?LogoTransaction
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

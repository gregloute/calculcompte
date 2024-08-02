<?php

namespace App\Repository;

use App\Entity\Mois;
use App\Entity\Transaction;
use App\Entity\TransactionSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    /**
     * @param $propriete
     * @param $signe
     * @param $valeur
     * @return Transaction[]
     */
    public function getTransactionParPropriete($propriete, $signe, $valeur){
        return $this->createQueryBuilder('m')
            ->andWhere('m.'.$propriete.' '.$signe.' :val')
            ->setParameter('val', $valeur)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param $search
     * @param Mois $mois
     * @return Transaction[]
     */
    public function getTransactionBySearch(TransactionSearch $search, Mois $mois){
        $query = $this->createQueryBuilder('t')
            ->AndWhere('t.mois = :id')
            ->setParameter('id', $mois->getId());

            if ($search->isdepense() and $search->isRevenu()){
                if ($search->isdepense()){
                    $query = $query
                        ->andWhere('t.depense = :isdep')
                        ->setParameter('isdep', $search->isdepense());
                }
                if ($search->isRevenu()){
                    $query = $query
                        ->orWhere('t.depense = :isrevenu')
                        ->setParameter('isrevenu', 0)
                        ->AndWhere('t.mois = :id')
                        ->setParameter('id', $mois->getId());
                }
                if ($search->getPrice()) {
                    $query = $query
                        ->AndWhere('t.valeur LIKE :valeur')
                        ->setParameter('valeur', str_replace('.0','','%'.$search->getPrice()));
                }
                if ($search->getMotsName() and is_array($search->getMotsName())){
                    foreach ($search->getMotsName() as $key => $mot){
                        $query = $query
                            ->andWhere('t.nom LIKE :mot_'.$key)
                            ->setParameter('mot_'.$key, '%'.$mot.'%');
                    }
                }

            }
            else{
                if ($search->getPrice()) {
                    $query = $query
                        ->AndWhere('t.valeur LIKE :valeur')
                        ->setParameter('valeur', str_replace('.0','','%'.$search->getPrice()));
                }
                if ($search->getMotsName() and is_array($search->getMotsName())){
                    foreach ($search->getMotsName() as $key => $mot){
                        $query = $query
                            ->andWhere('t.nom LIKE :mot_'.$key)
                            ->setParameter('mot_'.$key, '%'.$mot.'%');
                    }
                }
                if ($search->isdepense()){
                    $query = $query
                        ->andWhere('t.depense = :isdep')
                        ->setParameter('isdep', $search->isdepense());
                }
                if ($search->isRevenu()){
                    $query = $query
                        ->andWhere('t.depense = :isrevenu')
                        ->setParameter('isrevenu', 0);
                }
            }
            dump($query
                ->addOrderBy('t.surcompte', 'ASC')
                ->getQuery());
            return $query
                ->addOrderBy('t.surcompte', 'ASC')
                ->getQuery()
                ->getResult();
    }

    // /**
    //  * @return Transaction[] Returns an array of Transaction objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Transaction
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

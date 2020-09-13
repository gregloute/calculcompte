<?php

namespace App\Repository;

use App\Entity\Mois;
use App\Entity\MoisSearch;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|Mois find($id, $lockMode = null, $lockVersion = null)
 * @method null|Mois findOneBy(array $criteria, array $orderBy = null)
 * @method Mois[]    findAll()
 * @method Mois[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MoisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mois::class);
    }

    /**
     * @return Mois
     */
    public function findLatest(): ?Mois
    {
        return $this->createQueryBuilder('m')
            ->orderBy('m.created_at', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @param $propriete
     * @param $signe
     * @param $valeur
     * @return Mois[]
     */
    public function getMoisParPropriete($propriete, $signe, $valeur){
        return $this->createQueryBuilder('m')
            ->andWhere('m.'.$propriete.' '.$signe.' :val')
            ->setParameter('val', $valeur)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param $propriete
     * @param $signe
     * @param $valeur
     * @return Mois
     */
    public function getDernierMoisParPropriete($propriete, $signe, $valeur){
            $result = $this->createQueryBuilder('m')
            ->andWhere('m.'.$propriete.' '.$signe.' :val')
            ->setParameter('val', $valeur)
            ->orderBy('m.created_at', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
            ;
            foreach ($result as $mois){
                return $mois;
            }
    }

    /**
     * @param MoisSearch $search
     * @param Utilisateur $utilisateur
     * @return Mois[]
     */
    public function getMoisBySearch(MoisSearch $search, Utilisateur $utilisateur){
        $query = $this->createQueryBuilder('m')
            ->AndWhere('m.user = :id')
            ->setParameter('id', $utilisateur->getId());
        if ($search->getMotsName() and is_array($search->getMotsName())){
            foreach ($search->getMotsName() as $key => $mot){
                $query = $query
                    ->andWhere('m.nom LIKE :mot_'.$key)
                    ->setParameter('mot_'.$key, '%'.$mot.'%');
            }
        }
        return $query
            ->addOrderBy('m.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Mois[] Returns an array of Mois objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Mois
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

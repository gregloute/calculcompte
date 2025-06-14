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
    public function getTransactionParPropriete($propriete, $signe, $valeur): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.'.$propriete.' '.$signe.' :val')
            ->setParameter('val', $valeur)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * Récupère les transactions pour un mois donné en fonction des critères de recherche.
     * Cette fonction est optimisée pour effectuer une seule requête à la base de données.
     *
     * @param TransactionSearch $search L'objet contenant les critères de recherche.
     * @param Mois $mois L'entité Mois pour filtrer les transactions.
     * @return Transaction[] Retourne un tableau d'objets Transaction.
     */
    public function getTransactionBySearch(TransactionSearch $search, Mois $mois): array
    {
        // 1. Initialise le QueryBuilder en filtrant toujours par le mois.
        // C'est le point de départ de notre requête unique.
        $queryBuilder = $this->createQueryBuilder('t')
            ->addSelect('l') // IMPORTANT : Ajoute la sélection de l'entité Logo
            ->leftJoin('t.logo', 'l') // IMPORTANT : Effectue une jointure LEFT JOIN sur la relation 'logo'
            ->andWhere('t.mois = :moisId')
            ->setParameter('moisId', $mois->getId());

        // 2. Gère les filtres de dépenses et de revenus.
        // Cette logique assure que nous filtrons correctement sans duplication de conditions.
        // - Si isdepense() est vrai et isRevenu() est faux : nous voulons seulement les dépenses.
        if ($search->isdepense() && !$search->isRevenu()) {
            $queryBuilder
                ->andWhere('t.depense = :isDepense')
                ->setParameter('isDepense', true); // Assumant que 'depense' est un booléen ou 1 pour vrai
        }
        // - Si isdepense() est faux et isRevenu() est vrai : nous voulons seulement les revenus.
        //   L'original utilisait '0' pour revenu, ce qui est cohérent avec 'false' pour un booléen.
        elseif (!$search->isdepense() && $search->isRevenu()) {
            $queryBuilder
                ->andWhere('t.depense = :isRevenu')
                ->setParameter('isRevenu', false); // Assumant que 'depense' est un booléen ou 0 pour faux/revenu
        }
        // - Si isdepense() est vrai ET isRevenu() est vrai, OU si les deux sont faux :
        //   Aucun filtre spécifique n'est appliqué sur 't.depense'. Cela inclut toutes les transactions
        //   pour le mois, qu'elles soient dépenses ou revenus.

        // 3. Gère le filtre par valeur (prix).
        // Ajoute les '%' aux deux extrémités pour une recherche "contient" avec LIKE.
        if ($search->getPrice()) {
            $queryBuilder
                ->andWhere('t.valeur LIKE :valeur')
                ->setParameter('valeur', '%' . str_replace('.0', '', $search->getPrice()) . '%');
        }

        // 4. Gère le filtre par mots-clés dans le nom (t.nom).
        // Utilise 'orX' pour trouver les transactions contenant n'importe lequel des mots fournis.
        if ($search->getMotsName() && is_array($search->getMotsName())) {
            $orConditions = []; // Tableau pour stocker les conditions OR
            foreach ($search->getMotsName() as $key => $mot) {
                $parameterName = 'mot_' . $key; // Crée un nom de paramètre unique pour chaque mot
                $orConditions[] = 't.nom LIKE :' . $parameterName;
                $queryBuilder->setParameter($parameterName, '%' . $mot . '%');
            }
            // Applique les conditions OR groupées s'il y en a.
            if (!empty($orConditions)) {
                $queryBuilder->andWhere($queryBuilder->expr()->orX(...$orConditions));
            }
        }

        // 5. Applique les ordres de tri.
        // Ceux-ci sont ajoutés une seule fois à la fin.
        $queryBuilder
            ->addOrderBy('t.surcompte', 'ASC')
            ->addOrderBy('t.created_at', 'DESC');

        // 6. Exécute la requête et retourne les résultats.
        return $queryBuilder->getQuery()->getResult();
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

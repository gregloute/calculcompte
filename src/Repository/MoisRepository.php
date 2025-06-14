<?php

namespace App\Repository;

use App\Entity\Mois;
use App\Entity\MoisSearch;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;

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
    public function getMoisBySearch(MoisSearch $search, Utilisateur $utilisateur): array
    {
        $queryBuilder = $this->createQueryBuilder('m')
            ->andWhere('m.user = :id')
            ->setParameter('id', $utilisateur->getId());

        $monthNameLikeConditions = [];
        $transactionNameLikeConditions = [];

        // Prépare les conditions LIKE pour chaque mot-clé de recherche.
        // Chaque mot-clé sera appliqué avec un opérateur AND pour le nom du mois et le nom de la transaction.
        if ($search->getMotsName() && is_array($search->getMotsName())) {
            foreach ($search->getMotsName() as $key => $mot) {
                $paramName = ':mot_' . $key;
                $queryBuilder->setParameter($paramName, '%' . $mot . '%');

                // Conditions pour le nom du mois (ex: m.nom LIKE '%janvier%' AND m.nom LIKE '%repas%')
                $monthNameLikeConditions[] = $queryBuilder->expr()->like('m.nom', $paramName);

                // Conditions pour le nom de la transaction (ex: t.nom LIKE '%janvier%' AND t.nom LIKE '%repas%')
                $transactionNameLikeConditions[] = $queryBuilder->expr()->like('t.nom', $paramName);
            }
        }

        // Applique les jointures et conditions si des mots-clés de recherche sont fournis.
        if (!empty($monthNameLikeConditions)) { // Vérifie si des mots de recherche existent

            // Construit la clause WITH pour la jointure des transactions.
            // Seules les transactions dont le nom contient TOUS les mots-clés seront jointes.
            // Cela filtre les transactions avant même qu'elles ne soient hydratées.
            $transactionJoinWithClause = $queryBuilder->expr()->andX(...$transactionNameLikeConditions);

            // Effectue une jointure à gauche avec les transactions (`t`), filtrées par la clause `WITH`.
            // `addSelect('t')` garantira que seules ces transactions filtrées seront hydratées
            // dans la collection `m.transactions` de l'entité `Mois`.
            $queryBuilder->leftJoin(
                'm.transactions',
                't',
                Expr\Join::WITH,
                $transactionJoinWithClause
            );
            $queryBuilder->addSelect('t');

            // --- Construction de la clause WHERE principale pour les mois ---
            // Le mois sera inclus dans les résultats s'il satisfait l'une des conditions suivantes :

            // Condition 1: Le nom du mois contient TOUS les mots-clés de recherche.
            $monthMatchesAllTerms = $queryBuilder->expr()->andX(...$monthNameLikeConditions);

            // Condition 2: Le mois a au moins une transaction qui correspond à TOUS les mots-clés.
            // Grâce au `LEFT JOIN` avec la clause `WITH`, `t.id` sera non nul (`isNotNull`)
            // seulement si une transaction répondant aux critères de la clause `WITH` a été jointe.
            $monthHasMatchingTransaction = $queryBuilder->expr()->isNotNull('t.id');

            // Les mois doivent correspondre au nom DU MOIS OU avoir une transaction correspondante.
            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $monthMatchesAllTerms,
                    $monthHasMatchingTransaction
                )
            );
        } else {
            // Si aucun mot-clé de recherche n'est fourni, le comportement par défaut est de
            // renvoyer tous les mois de l'utilisateur avec toutes leurs transactions non filtrées.
            $queryBuilder->leftJoin('m.transactions', 't')->addSelect('t');
        }

        // Ajoute `distinct()` pour éviter les doublons de mois.
        // Ceci est important si, par exemple, un mois contient plusieurs transactions qui correspondent
        // aux critères de recherche; sans `distinct()`, le mois pourrait apparaître plusieurs fois.
        $queryBuilder->distinct();

        return $queryBuilder
            ->addOrderBy('m.created_at', 'DESC') // Trie les résultats par date de création du mois (du plus récent au plus ancien).
            ->getQuery()
            ->getResult(); // Exécute la requête et retourne les objets Mois.
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

<?php

namespace App\Controller;

use App\Entity\LogoTransaction;
use App\Entity\Mois;
use App\Entity\MoisSearch;
use App\Entity\Transaction;
use App\Entity\TransactionSearch;
use App\Form\MoisSearchType;
use App\Form\MoisType;
use App\Form\NewTransactionType;
use App\Form\TransactionSearchType;
use App\Form\TransactionType;
use App\Repository\LogoTransactionRepository;
use App\Repository\MoisRepository;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class MoisController extends AbstractController
{
    /**
     * @var MoisRepository
     */
    private $repository;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(MoisRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @param Request $request
     * @return Response
     */
    #[Route(path: '/mois', name: 'mois#index')]
    public function index(Request $request): Response
    {
        $isSearch = false;
        $search = new MoisSearch();
        $form = $this->createForm(MoisSearchType::class, $search);
        $form->handleRequest($request);
        if ($form->isSubmitted()){
            $isSearch = true;
        }

        $user = $this->getUser();
        $moiss = $this->repository->getMoisBySearch($search, $user);

        return $this->render('mois/index.html.twig', [
            'moiss' => $moiss,
            'form' => $form->createView(),
            'isSearch' => $isSearch
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    #[Route(path: '/mois/new/', name: 'mois#new')]
    public function new(Request $request): Response
    {
        $mois = new Mois();
        $form = $this->createForm(MoisType::class, $mois);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user = $this->getUser();
            $mois->setUser($user);

            $dernierMois = $this->repository->getDernierMoisParPropriete('user','=', $mois->getUser()->getId());
            if (!is_null($dernierMois)) {
                foreach ($dernierMois->getTransactions() as $transaction){
                    if ($transaction->getRecurrent()){
                        if ($transaction->isEndTransaction()){
                            continue;
                        }
                        $newTransaction = new Transaction();
                        $newTransaction->setSurcompte(false)
                            ->setDepense($transaction->getDepense())
                            ->setValeur($transaction->getValeur(true))
                            ->setNom($transaction->getNom())
                            ->setRecurrent($transaction->getRecurrent())
                            ->setLogo($transaction->getLogo())
                            ->setEndAt($transaction->getEndAt())
                        ;

                        $mois->addTransaction($newTransaction);
                        $mois->setSolde($mois->getSolde() + $newTransaction->getValeur(true));
                        $this->em->persist($newTransaction);
                    }
                }
            }

            $this->em->persist($mois);
            $this->em->flush();
            $this->addFlash('success', 'Le mois a bien été créé.');
            return $this->redirectToRoute('mois#show',['id'=>$mois->getId()]);
        }

        return $this->render('mois/new.html.twig', [
            "form" => $form->createView(),
        ]);
    }

    /**
     * Affiche les détails d'un mois et ses transactions, avec des optimisations de requêtes.
     *
     * @param int $id L'ID du mois à afficher (le ParamConverter ne sera plus utilisé pour charger le mois ici).
     * @param Request $request
     * @param TransactionRepository $transactionRepository Le dépôt pour les transactions.
     * @param MoisRepository $moisRepository Le dépôt pour les mois, utilisé pour le chargement "eager".
     * @return Response
     */
    #[Route(path: '/mois/{id}', name: 'mois#show')]
    public function show(
        int $id, // Nous prenons l'ID directement pour charger le Mois nous-mêmes
        Request $request,
        TransactionRepository $transactionRepository,
        MoisRepository $moisRepository // Injection du MoisRepository
    ): Response {
        // 1. Charger le Mois avec l'utilisateur associé pour éviter le lazy loading.
        // Ceci remplace le chargement par défaut du ParamConverter pour cette route spécifique,
        // garantissant que la relation 'user' du mois est déjà chargée.
        $mois = $moisRepository->findOneByIdWithUser($id);

        // Si le mois n'est pas trouvé, lance une exception 404.
        if (!$mois) {
            throw new NotFoundHttpException('Le mois demandé n\'existe pas.');
        }

        // 2. Vérification de l'accès (utilisateur courant vs utilisateur du mois).
        // Le user du mois est déjà chargé, donc pas de requête supplémentaire ici.
        $currentUser = $this->getUser();
        if ($mois->getUser()->getId() !== $currentUser->getId()){
            // Redirection vers une route d'index, par exemple, si l'accès est refusé.
            // Assurez-vous que 'mois_index' est une route valide.
            return $this->redirectToRoute('mois#index');
        }

        // 3. Traitement du formulaire de recherche de transactions.
        $search = new TransactionSearch();
        $form = $this->createForm(TransactionSearchType::class, $search);
        $form->handleRequest($request);

        // 4. Récupération des transactions avec la fonction optimisée (une seule requête ici).
        $transactions = $transactionRepository->getTransactionBySearch($search, $mois);

        // 5. Rendu de la vue Twig.
        return $this->render('mois/show.html.twig', [
            'mois' => $mois,
            'form' => $form->createView(),
            'transactions' => $transactions
        ]);
    }

    /**
     * @param Mois $mois
     * @param Request $request
     * @return Response
     */
    #[Route(path: '/mois/edit/{id}', name: 'mois#edit', methods: 'GET|POST')]
    public function editMois(Mois $mois, Request $request): Response
    {
        $form = $this->createForm(MoisType::class, $mois);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $mois->setUpdatedAt(new \DateTime());
            $this->em->persist($mois);
            $this->em->flush();
            return $this->redirectToRoute('mois#show',['id'=>$mois->getId()]);
        }

        return $this->render('mois/edit.html.twig', [
            'mois' => $mois,
            "form" => $form->createView()
        ]);
    }

    /**
     * @param Mois $mois
     * @param Request $request
     * @return Response
     */
    #[Route(path: '/mois/del/{id}', name: 'mois#delMois', methods: 'DELETE')]
    public function delMois(Mois $mois, Request $request)
    {

        if($this->isCsrfTokenValid('delete'.$mois->getId(), $request->get('_token'))){

            $this->em->remove($mois);
            $this->em->flush();
            $this->addFlash('success', 'Le mois a bien été supprimé.');

        }
        return $this->redirectToRoute('mois#index');
    }

    /**
     * @param Transaction $transaction
     * @param Request $request
     * @param TransactionRepository $repository
     * @return Response
     */
    #[Route(path: '/mois/transaction/edit/{id}', name: 'mois#editTransaction', methods: 'GET|POST')]
    public function editTransaction(Transaction $transaction, Request $request, TransactionRepository $repository): Response
    {
        $form = $this->createForm(TransactionType::class, $transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $transaction->setUpdatedAt(new \DateTime());
            $transaction->getMois()->setUpdatedAt(new \DateTime());

            $holdValeur = $request->get('_holdValue');
            $holdDep = (boolval($request->get('_holdDep'))) ;

            /* on re rajoute la valeur au solde du mois */

            if ($holdDep){
                $solde = $transaction->getMois()->getSolde() + $holdValeur;
            }else{
                $solde = $transaction->getMois()->getSolde() - $holdValeur;
            }

            $transaction->getMois()->setSolde($solde);

            if ($transaction->getDepense()){
                $transaction->setValeur("-".$transaction->getValeur());
            }else{
                $transaction->setValeur($transaction->getValeur());
            }

            $transaction->getMois()->setSolde($transaction->getMois()->getSolde()+$transaction->getValeur(true));
            $this->em->persist($transaction);
            $this->em->flush();
            return $this->redirectToRoute('mois#show',['id'=>$transaction->getMois()->getId()]);
        }

        return $this->render('mois/transacton/edit.html.twig', [
            'transaction' => $transaction,
            "form" => $form->createView()
        ]);
    }

    /**
     * @param Mois $mois
     * @param Request $request
     * @return Response
     */
    #[Route(path: '/mois/transaction/new-{id}', name: 'mois#newTransaction')]
    public function newTransaction(Mois $mois, Request $request): Response
    {
        $transaction = new Transaction();
        $form = $this->createForm(NewTransactionType::class, $transaction);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $mois->setUpdatedAt(new \DateTime());

            if ($transaction->getDepense()){
                $transaction->setValeur("-".$transaction->getValeur());
            }

            $mois->setSolde($mois->getSolde() + $transaction->getValeur(true));
            $transaction->setMois($mois);
            $this->em->persist($mois);
            $this->em->persist($transaction);
            $this->em->flush();
            $this->addFlash('success', 'La transaction a bien été crée.');
            return $this->redirectToRoute('mois#show',['id'=>$mois->getId()]);
        }

        return $this->render('mois/transacton/new.html.twig', [
            'transaction' => $transaction,
            "form" => $form->createView()
        ]);
    }

    /**
     * @param Transaction $transaction
     * @param Request $request
     * @return Response
     */
    #[Route(path: '/mois/transaction/del/{id}', name: 'mois#delTransaction', methods: 'DELETE')]
    public function delTransaction(Transaction $transaction, Request $request)
    {

        if($this->isCsrfTokenValid('delete'.$transaction->getId(), $request->get('_token'))){
            $mois = $transaction->getMois();
            $mois->setUpdatedAt(new \DateTime());

            if ($transaction->getDepense()){
                $mois->setSolde($mois->getSolde()+$transaction->getValeur());
            }else{
                $mois->setSolde($mois->getSolde()-$transaction->getValeur());
            }

            $this->em->persist($mois);
            $this->em->remove($transaction);
            $this->em->flush();
            $this->addFlash('success', 'La transaction a bien été supprimé.');

        }
        return $this->redirectToRoute('mois#show',['id'=>$mois->getId()]);
    }
}

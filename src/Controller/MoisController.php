<?php

namespace App\Controller;

use App\Entity\Mois;
use App\Entity\Transaction;
use App\Entity\Utilisateur;
use App\Form\MoisType;
use App\Form\TransactionType;
use App\Repository\MoisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @Route("/mois", name="mois#index")
     * @return Response
     */
    public function index(): Response
    {

        $user = $this->getUser();
        $moiss = $user->getMois();

        return $this->render('mois/index.html.twig', [
            'moiss' => $moiss,
        ]);
    }

    /**
     * @Route("/mois/new/", name="mois#new")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $mois = new Mois();
        $form = $this->createForm(MoisType::class, $mois);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user = $this->getUser();
            $mois->setUser($user);
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
     * @Route("/mois/{id}", name="mois#show")
     * @param Mois $mois
     * @return Response
     */
    public function show(Mois $mois): Response
    {
        $user = $this->getUser();
        if ($mois->getUser()->getId() !== $user->getId()){
            return $this->redirectToRoute('mois#index');
        }

        return $this->render('mois/show.html.twig', [
            'mois' => $mois,
        ]);
    }

    /**
     * @Route("/mois/edit/{id}", name="mois#edit", methods="GET|POST")
     * @param Mois $mois
     * @param Request $request
     * @return Response
     */
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
     * @Route("/mois/del/{id}", name="mois#delMois", methods="DELETE")
     * @param Mois $mois
     * @param Request $request
     * @return Response
     */
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
     * @Route("/mois/transaction/edit/{id}", name="mois#editTransaction", methods="GET|POST")
     * @param Transaction $transaction
     * @param Request $request
     * @return Response
     */
    public function editTransaction(Transaction $transaction, Request $request): Response
    {
        $form = $this->createForm(TransactionType::class, $transaction);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $transaction->setUpdatedAt(new \DateTime());
            $transaction->getMois()->setUpdatedAt(new \DateTime());

            /* on re rajoute la valeur au solde du mois */
            $solde = $transaction->getMois()->getSolde() - $transaction->getValeur(true);
            $transaction->getMois()->setSolde($solde);

            if ($transaction->getDepense()){
                $transaction->setValeur("-".$transaction->getValeur());
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
     * @Route("/mois/transaction/new-{id}", name="mois#newTransaction")
     * @param Mois $mois
     * @param Request $request
     * @return Response
     */
    public function newTransaction(Mois $mois, Request $request): Response
    {
        $transaction = new Transaction();
        $form = $this->createForm(TransactionType::class, $transaction);
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
     * @Route("/mois/transaction/del/{id}", name="mois#delTransaction", methods="DELETE")
     * @param Transaction $transaction
     * @param Request $request
     * @return Response
     */
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

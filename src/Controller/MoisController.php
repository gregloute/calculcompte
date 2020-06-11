<?php

namespace App\Controller;

use App\Entity\Mois;
use App\Entity\Transaction;
use App\Repository\MoisRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MoisController extends AbstractController
{
    /**
     * @Route("/mois", name="mois#index")
     * @param MoisRepository $repository
     * @return Response
     */
    public function index(MoisRepository $repository)
    {
        $moiss = $repository->findAll();

        return $this->render('mois/index.html.twig', [
            'moiss' => $moiss,
        ]);
    }

    /**
     * @Route("/mois/{id}", name="mois#show")
     * @param MoisRepository $repository
     * @param Mois $mois
     * @return Response
     */
    public function show(MoisRepository $repository, Mois $mois)
    {

        dump($mois);

        return $this->render('mois/show.html.twig', [
            'mois' => $mois,
        ]);
    }

    /**
     * @Route("/mois/transaction/{id}", name="mois#editTransaction")
     * @param MoisRepository $repository
     * @param Transaction $transaction
     * @return Response
     */
    public function editTransaction(MoisRepository $repository, Transaction $transaction)
    {

        dump($transaction);

        return $this->render('mois/transacton/edit.html.twig', [
            'transaction' => $transaction,
        ]);
    }
}

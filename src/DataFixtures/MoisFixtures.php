<?php

namespace App\DataFixtures;

use App\Entity\Mois;
use App\Entity\Transaction;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MoisFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $m1 = new Mois();
        $m1->setNom('Juin')
            ->setSolde(0)
        ;

        $manager->persist($m1);

        $t1 = new Transaction();
        $t1->setNom('caf')
            ->setValeur(300)
            ->setDepense(false)
            ->setSurcompte(true)
            ->setRecurrent(true)
        ;

        $manager->persist($t1);

        $t2 = new Transaction();
        $t2->setNom('edf')
            ->setValeur(49.5)
            ->setDepense(true)
            ->setSurcompte(false)
            ->setRecurrent(false)
        ;

        $manager->persist($t2);

        $m2 = new Mois();
        $m2->setNom('Juillet')
            ->setSolde(250.50)
            ->addTransaction($t1)
            ->addTransaction($t2)
        ;

        $manager->persist($m2);
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}

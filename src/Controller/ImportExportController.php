<?php

namespace App\Controller;

use App\Entity\Mois;
use App\Entity\Transaction;
use App\Form\ImportType;
use App\Repository\MoisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ImportExportController extends AbstractController
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
    #[Route(path: '/import-export/import', name: 'import_export#import')]
    public function import(Request $request)
    {
        $user = $this->getUser();
        $dir = __DIR__.'/../../public/tmp';
        $form = $this->createForm(ImportType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            $data['file']->move($dir,$data['file']->getClientOriginalName());
            $file = $dir.'/'.$data['file']->getClientOriginalName();
            // mettre le contenu du fichier dans une variable
            $data = file_get_contents($file);
            // décoder le flux JSON
            $obj = json_decode($data);

            foreach ($obj->mois as $m){
                $mois = new Mois();
                $mois->setNom($m->nom)->setSolde($m->solde)->setUser($user);
                foreach ($m->transactions as $t){
                    $transaction = new Transaction();
                    $transaction->setNom($t->nom)->setValeur($t->valeur)->setDepense($t->dep)->setSurcompte($t->surCompte);
                    $mois->addTransaction($transaction);
                }
                $this->em->persist($mois);
            }
            $this->em->flush();
            unlink($file);
            return $this->redirectToRoute('mois#index');
        }

        return $this->render('import_export/import.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return Response
     */
    #[Route(path: '/import-export/export', name: 'import_export#export')]
    public function export()
    {
        $user = $this->getUser();
        $data = $user->getMois();

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        $json = $serializer->serialize($data, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        $response = new Response();

        $response->setContent($json);
        $response->headers->set('Content-type', 'application/json');
        $response->headers->set('Content-disposition', 'attachment; filename=Data.calculcompte');

        return $response;
    }
}

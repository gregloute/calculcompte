<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\InscriptionType;
use App\Repository\MoisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UtilisateurController extends AbstractController
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
     * @param UserPasswordHasherInterface $passwordHasher
     * @return Response
     */
    #[Route(path: '/inscription', name: 'inscription')]
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher)
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(InscriptionType::class, $utilisateur);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $passwordCrypte = $passwordHasher->hashPassword($utilisateur,$utilisateur->getPassword());
            $utilisateur->setPassword($passwordCrypte);
            $array = array(
                "1" => "ROLE_USER"
            );
            $utilisateur->setRoles($array);
            $this->em->persist($utilisateur);
            $this->em->flush();
            return $this->redirectToRoute('mois#index');
        }
        return $this->render('utilisateur/inscription.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param AuthenticationUtils $utils
     * @return Response
     */
    #[Route(path: '/login', name: 'connexion')]
    public function login(AuthenticationUtils $utils): Response
    {
        return $this->render('utilisateur/login.html.twig',[
            "lastUserName" => $utils->getLastUsername(),
            "error" => $utils->getLastAuthenticationError()
        ]);
    }

    #[Route(path: '/logout', name: 'deconnexion')]
    public function logout()
    {

    }
}

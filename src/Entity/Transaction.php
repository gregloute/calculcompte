<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * @ORM\Entity(repositoryClass=TransactionRepository::class)
 */
class Transaction
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="float")
     */
    private $valeur;

    /**
     * @ORM\Column(type="boolean")
     */
    private $depense;

    /**
     * @ORM\Column(type="boolean")
     */
    private $surcompte;

    /**
     * @ORM\Column(type="boolean")
     */
    private $recurrent;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity=Mois::class, inversedBy="transactions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $mois;

    public function __construct()
    {
        $this->surcompte = false;
        $this->recurrent = false;
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getValeur(Bool $isCalcule = null): ?float
    {
        if ($isCalcule){
            return $this->valeur;
        }else{
            return floatval(str_replace('-','',$this->valeur));
        }
    }

    public function setValeur(float $valeur): self
    {
        $this->valeur = $valeur;

        return $this;
    }

    public function getDepense(): ?bool
    {
        return $this->depense;
    }

    public function setDepense(bool $depense): self
    {
        $this->depense = $depense;

        return $this;
    }

    public function getSurcompte(): ?bool
    {
        return $this->surcompte;
    }

    public function setSurcompte(bool $surcompte): self
    {
        $this->surcompte = $surcompte;

        return $this;
    }

    public function getRecurrent(): ?bool
    {
        return $this->recurrent;
    }

    public function setRecurrent(bool $recurrent): self
    {
        $this->recurrent = $recurrent;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getMois(): ?Mois
    {
        return $this->mois;
    }

    public function setMois(?Mois $mois): self
    {
        $this->mois = $mois;

        return $this;
    }
}

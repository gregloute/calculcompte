<?php

namespace App\Entity;

use App\Repository\MoisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MoisRepository::class)]
class Mois
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $nom;

    #[ORM\Column(type: 'float')]
    private $solde;

    #[ORM\OneToMany(targetEntity: Transaction::class, mappedBy: 'mois', orphanRemoval: true, cascade: ['persist'])]
    #[ORM\OrderBy(['created_at' => 'DESC'])]
    private $transactions;

    #[ORM\Column(type: 'datetime')]
    private $created_at;

    #[ORM\Column(type: 'datetime')]
    private $updated_at;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'mois')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    public function __construct()
    {
        $this->solde = 0;
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
        $this->transactions = new ArrayCollection();
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

    public function getSolde(): ?float
    {
        return $this->solde;
    }

    public function setSolde(float $solde): self
    {
        $this->solde = $solde;

        return $this;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setMois($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->contains($transaction)) {
            $this->transactions->removeElement($transaction);
            // set the owning side to null (unless already changed)
            if ($transaction->getMois() === $this) {
                $transaction->setMois(null);
            }
        }

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

    public function getSoldeSurCompte(): float
    {
        $ts = $this->getTransactions();

        $solde = 0;
        foreach ($ts as $t) {

            if ($t->getSurcompte()){
                $solde += $t->getValeur(true);
            }

        }

        return $solde;
    }

    public function getNombreDepenseAVenir(): int
    {
        $ts = $this->getTransactions();

        $nbDep = 0;
        foreach ($ts as $t){
            if ($t->getDepense() and !$t->getSurcompte()){
                $nbDep++;
            }
        }
        return $nbDep;
    }

    public function getNombreDepenseTotal(): int
    {
        $ts = $this->getTransactions();

        $nbDep = 0;
        foreach ($ts as $t){
            if ($t->getDepense()){
                $nbDep++;
            }
        }
        return $nbDep;
    }

    public function getDepenseTotal(): float
    {
        $ts = $this->getTransactions();

        $totalDep = 0;
        foreach ($ts as $t){
            if ($t->getDepense()){
                $val = str_replace('-','',$t->getValeur());
                $totalDep+= $val;
            }
        }
        return $totalDep;
    }

    public function getRevenuTotal(): float
    {
        $ts = $this->getTransactions();

        $totalRev = 0;
        foreach ($ts as $t){
            if (!$t->getDepense()){
                $val = $t->getValeur();
                $totalRev+= $val;
            }
        }
        return $totalRev;
    }

    /**
     * @return Collection
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function getUser(): ?Utilisateur
    {
        return $this->user;
    }

    public function setUser(?Utilisateur $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function soldNegatif(): bool
    {
        $bool = false;
        $sold = StrVal($this->solde);

        if(str_contains($sold, '-'))
        {
            $bool = true;
        }

        return $bool;
    }
}

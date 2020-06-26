<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UtilisateurRepository::class)
 * @UniqueEntity(
 *     fields={"username"},
 *     message="Le nom d'utilisateur existe déjà"
 *     )
 */
class Utilisateur implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="5",max="10",minMessage="Il faut plus de 5 carac",maxMessage="Il faut moins de 10 carac")
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="5",max="20",minMessage="Il faut plus de 5 carac",maxMessage="Il faut moins de 20 carac")
     */
    private $password;

    /**
     * @Assert\Length(min="5",max="20",minMessage="Il faut plus de 5 carac",maxMessage="Il faut moins de 20 carac")
     * @Assert\EqualTo(propertyPath="password",message="Les mdp ne sont pas équivalents")
     */
    private $passwordConfirm;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $roles;

    /**
     * @ORM\OneToMany(targetEntity=Mois::class, mappedBy="user", orphanRemoval=true)
     */
    private $mois;

    public function __construct()
    {
        $this->mois = new ArrayCollection();
        $this->setRoles(null);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPasswordConfirm(): ?string
    {
        return $this->passwordConfirm;
    }

    public function setPasswordConfirm($passwordConfirm): self
    {
        $this->passwordConfirm = $passwordConfirm;

        return $this;
    }

    public function getRoles()
    {
        return [$this->roles];
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function setRoles(?string $roles): self
    {
        if ($roles === null){
            $this->roles = "ROLE_USER";
        }else{
            $this->roles = $roles;
        }

        return $this;
    }

    /**
     * @return Collection|Mois[]
     */
    public function getMois(): Collection
    {
        return $this->mois;
    }

    public function addMoi(Mois $moi): self
    {
        if (!$this->mois->contains($moi)) {
            $this->mois[] = $moi;
            $moi->setUser($this);
        }

        return $this;
    }

    public function removeMoi(Mois $moi): self
    {
        if ($this->mois->contains($moi)) {
            $this->mois->removeElement($moi);
            // set the owning side to null (unless already changed)
            if ($moi->getUser() === $this) {
                $moi->setUser(null);
            }
        }

        return $this;
    }
}

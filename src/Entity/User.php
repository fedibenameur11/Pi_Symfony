<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'veuillez remplir le champ du nom')]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Il faut inserer au moins {{ limit }} characteres',
        maxMessage: 'Il faut inserer au maximum {{ limit }} characteres',
    )]
    #[Assert\Type(
        type:"string",
        message:"veuillez inserer un nom correct "
    )]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'veuillez remplir le champ du prenom')]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Il faut inserer au moins {{ limit }} characteres',
        maxMessage: 'Il faut inserer au maximum {{ limit }} characteres',
    )]
    #[Assert\Type(
        type:"string",
        message:"veuillez inserer un prenom correct "
    )]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'veuillez remplir le champ du mail')]
    #[Assert\Email(
        message: 'The email {{ value }} is not a valid email.',
    )]
    private ?string $email = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'veuillez remplir le champ du téléphone')]
    #[Assert\Type(
        type:"integer",
        message:"veuillez inserer un numero valide "
    )]
    private ?int $telephone = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CategorieUser $categorie_user = null;

    #[ORM\OneToMany(mappedBy: 'coach', targetEntity: AbonnementCoach::class)]
    private Collection $abonnementCoaches;

    public function __construct()
    {
        $this->abonnementCoaches = new ArrayCollection();
    }

    public function __toString() {
        return $this->nom;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getTelephone(): ?int
    {
        return $this->telephone;
    }

    public function setTelephone(int $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getCategorieUser(): ?CategorieUser
    {
        return $this->categorie_user;
    }

    public function setCategorieUser(?CategorieUser $categorie_user): self
    {
        $this->categorie_user = $categorie_user;

        return $this;
    }

    /**
     * @return Collection<int, AbonnementCoach>
     */
    public function getAbonnementCoaches(): Collection
    {
        return $this->abonnementCoaches;
    }

    public function addAbonnementCoach(AbonnementCoach $abonnementCoach): self
    {
        if (!$this->abonnementCoaches->contains($abonnementCoach)) {
            $this->abonnementCoaches->add($abonnementCoach);
            $abonnementCoach->setCoach($this);
        }

        return $this;
    }

    public function removeAbonnementCoach(AbonnementCoach $abonnementCoach): self
    {
        if ($this->abonnementCoaches->removeElement($abonnementCoach)) {
            // set the owning side to null (unless already changed)
            if ($abonnementCoach->getCoach() === $this) {
                $abonnementCoach->setCoach(null);
            }
        }

        return $this;
    }
    
}

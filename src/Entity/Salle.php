<?php

namespace App\Entity;

use App\Repository\SalleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: SalleRepository::class)]
class Salle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["salle","abonnements"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'veuillez remplir le champ du nom')]
    #[Assert\Length(
        min: 3,
        max: 20,
        minMessage: 'Il faut inserer au moins {{ limit }} characteres',
        maxMessage: 'Il faut inserer au maximum {{ limit }} characteres',
    )]
    #[Groups(["salle"])]
    private ?string $nom_salle = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'veuillez remplir le champ d adresse')]
    #[Assert\Length(
        min: 3,
        max: 30,
        minMessage: 'Il faut inserer au moins {{ limit }} characteres',
        maxMessage: 'Il faut inserer au maximum {{ limit }} characteres',
    )]
    #[Groups(["salle"])]
    private ?string $adresse_salle = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'veuillez remplir le champ de numero de telephone')]
   
    private ?int $num_telephone = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'veuillez remplir le champ de code postal')]
    private ?int $codepostal = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'veuillez remplir le champ de ville')]
    #[Groups(["salle"])]
    private ?string $ville = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'veuillez remplir le champ de prix')]
    
    private ?int $prix_abonnement = null;

    #[ORM\OneToMany(mappedBy: 'salle', targetEntity: AbonnementSalle::class)]
    private Collection $abonnements;

    public function __construct()
    {
        $this->abonnements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomSalle(): ?string
    {
        return $this->nom_salle;
    }

    public function setNomSalle(string $nom_salle): self
    {
        $this->nom_salle = $nom_salle;

        return $this;
    }

    public function getAdresseSalle(): ?string
    {
        return $this->adresse_salle;
    }

    public function setAdresseSalle(string $adresse_salle): self
    {
        $this->adresse_salle = $adresse_salle;

        return $this;
    }

    public function getNumTelephone(): ?int
    {
        return $this->num_telephone;
    }

    public function setNumTelephone(int $num_telephone): self
    {
        $this->num_telephone = $num_telephone;

        return $this;
    }

    public function getCodepostal(): ?int
    {
        return $this->codepostal;
    }

    public function setCodepostal(int $codepostal): self
    {
        $this->codepostal = $codepostal;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getPrixAbonnement(): ?int
    {
        return $this->prix_abonnement;
    }

    public function setPrixAbonnement(int $prix_abonnement): self
    {
        $this->prix_abonnement = $prix_abonnement;

        return $this;
    }
                                                                                                                                                                                                                                                                                                                                           
    public function getAbonnement(): Collection
    {
        return $this->abonnements;
    }

    public function addAbonnement(AbonnementSalle $abonnement): self
    {
        if (!$this->abonnements->contains($abonnement)) {
            $this->abonnements->add($abonnement);
            $abonnement->setSalle($this);
        }

        return $this;
    }

    public function removeAbonnement(AbonnementSalle $abonnement): self
    {
        if ($this->abonnements->removeElement($abonnement)) {
            // set the owning side to null (unless already changed)
            if ($abonnement->getSalle() === $this) {
                $abonnement->setSalle(null);
            }
        }

        return $this;
    }
    public function __toString()
    {
        return $this->getNomSalle();
    }
}

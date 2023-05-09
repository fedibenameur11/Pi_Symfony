<?php

namespace App\Entity;

use App\Repository\LivraisonsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LivraisonsRepository::class)]
class Livraisons
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
    private ?string $nom_livraisons = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\GreaterThanOrEqual(
        "today",
        message: 'La date de commande ne peut pas être antérieure à aujourd\'hui'
    )]
    private ?\DateTimeInterface $date_livraisons = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'veuillez remplir le champ de la duree')]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Il faut inserer au moins {{ limit }} characteres',
        maxMessage: 'Il faut inserer au maximum {{ limit }} characteres',
    )]
    private ?int $duree_livraison = null;

    #[ORM\ManyToOne(inversedBy: 'livraisons')]
    private ?Commandes $commande = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomLivraisons(): ?string
    {
        return $this->nom_livraisons;
    }

    public function setNomLivraisons(string $nom_livraisons): self
    {
        $this->nom_livraisons = $nom_livraisons;

        return $this;
    }

    public function getDateLivraisons(): ?\DateTimeInterface
    {
        return $this->date_livraisons;
    }

    public function setDateLivraisons(\DateTimeInterface $date_livraisons): self
    {
        $this->date_livraisons = $date_livraisons;

        return $this;
    }

    public function getDureeLivraison(): ?int
    {
        return $this->duree_livraison;
    }

    public function setDureeLivraison(int $duree_livraison): self
    {
        $this->duree_livraison = $duree_livraison;

        return $this;
    }


    public function getCommande(): ?Commandes
    {
        return $this->commande;
    }

    public function setCommande(?Commandes $commandes): self
    {
        $this->commande = $commandes;

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\CommandesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

#[ORM\Entity(repositoryClass: CommandesRepository::class)]
class Commandes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("commande")]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: 'La date de commande ne peut pas être vide')]
    #[Assert\GreaterThanOrEqual(
        "today",
        message: 'La date de commande ne peut pas être antérieure à aujourd\'hui'
    )]
    #[Groups("commande")]
    private ?\DateTimeInterface $date_commande = null;

    #[ORM\Column(length: 255)]

    #[Assert\NotBlank(message: 'veuillez remplir le champ du nom')]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Il faut inserer au moins {{ limit }} characteres',
        maxMessage: 'Il faut inserer au maximum {{ limit }} characteres',
    )]
    #[Groups("commande")]
    private ?string $nom_commande = null;


    #[ORM\OneToMany(mappedBy: 'livraisons', targetEntity: Livraisons::class)]
    private  Collection $livraisons;

    #[ORM\Column(nullable: true)]
    private ?int $etat = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCommande(): ?\DateTimeInterface
    {
        return $this->date_commande;
    }

    public function setDateCommande(\DateTimeInterface $date_commande): self
    {
        $this->date_commande = $date_commande;

        return $this;
    }

    public function getNomCommande(): ?string
    {
        return $this->nom_commande;
    }

    public function setNomCommande(string $nom_commande): self
    {
        $this->nom_commande = $nom_commande;

        return $this;
    }

    public function __construct()
    {
        $this->livraisons = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getNomCommande();
    }
    /**
     * @return Collection<int, Livraisons>
     */
    public function getLivraisons(): Collection
    {
        return $this->livraisons;
    }

    public function addLivraisons(Livraisons $livraisons): self
    {
        if (!$this->livraisons->contains($livraisons)) {
            $this->livraisons->add($livraisons);
            $livraisons->setCommande($this);
        }

        return $this;
    }

    public function removeLivraisons(Livraisons $livraisons): self
    {
        if ($this->livraisons->removeElement($livraisons)) {
            // set the owning side to null (unless already changed)
            if ($livraisons->getCommande() === $this) {
                $livraisons->setCommande(null);
            }
        }

        return $this;
    }

    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(?int $etat): self
    {
        $this->etat = $etat;

        return $this;
    }
}

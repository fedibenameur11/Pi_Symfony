<?php

namespace App\Entity;

use App\Repository\AbonnementSalleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AbonnementSalleRepository::class)]
class AbonnementSalle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("abonnements")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'veuillez remplir le champ de duree d abonnement')]
    #[Assert\Length(
        min: 3,
        max: 20,
        minMessage: 'Il faut inserer au moins {{ limit }} characteres',
        maxMessage: 'Il faut inserer au maximum {{ limit }} characteres',
    )]
    #[Assert\Type(
        type:"string",
        message:"veuillez inserer un nom correct "
    )]
    #[Groups("abonnements")]
    private ?string $duree_abonnement = null;

    #[ORM\ManyToOne(inversedBy: 'abonnements', targetEntity: Salle::class, cascade : ['persist'])]
    //#[ORM\JoinColumn(nullable:false)]
    //#[ORM\ManyToOne(inversedBy: 'abonnements', cascade : ['persist'])]
    #[ORM\JoinColumn(name:'salle_id', referencedColumnName:'id')]
    #[Assert\NotBlank(message: 'veuillez choisir la salle')]
    #[Groups("abonnements")]
    public ?Salle $salle = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDureeAbonnement(): ?string
    {
        return $this->duree_abonnement;
    }

    public function setDureeAbonnement(string $duree_abonnement): self
    {
        $this->duree_abonnement = $duree_abonnement;

        return $this;
    }

    public function getSalle(): ?Salle
    {
        return $this->salle;
    }

    public function setSalle(?Salle $salle): self
    {
        $this->salle = $salle;

        return $this;
    }
}

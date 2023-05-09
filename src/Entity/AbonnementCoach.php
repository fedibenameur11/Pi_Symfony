<?php

namespace App\Entity;

use App\Repository\AbonnementCoachRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Programme;
use App\Entity\ProgrammeSemaine;

#[ORM\Entity(repositoryClass: AbonnementCoachRepository::class)]
class AbonnementCoach
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("abonnement_coach")]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'abonnementCoaches')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    //#[Assert\Choice([3])]
    private ?Users $coach = null;

    #[ORM\ManyToOne(inversedBy: 'abonnementCoaches')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    //#[Assert\Choice([2])]
    private ?Users $client = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Groups("abonnement_coach")]
    private ?int $duree_abonnement = null;

    #[ORM\Column]
    #[Groups("abonnement_coach")]
    private ?bool $statut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups("abonnement_coach")]
    private ?\DateTimeInterface $duree_debut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups("abonnement_coach")]
    private ?\DateTimeInterface $duree_fin = null;

    #[ORM\OneToOne(mappedBy: 'abonnementCoach', cascade: ['persist', 'remove'])]
    private ?Programme $programme = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCoach(): ?Users
    {
        return $this->coach;
    }

    public function setCoach(?Users $coach): self
    {
        $this->coach = $coach;

        return $this;
    }

    public function getClient(): ?Users
    {
        return $this->client;
    }

    public function setClient(?Users $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getDureeAbonnement(): ?int
    {
        return $this->duree_abonnement;
    }

    public function setDureeAbonnement(?int $duree_abonnement): self
    {
        $this->duree_abonnement = $duree_abonnement;

        return $this;
    }

    public function isStatut(): ?bool
    {
        return $this->statut;
    }

    public function setStatut(bool $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getDureeDebut(): ?\DateTimeInterface
    {
        return $this->duree_debut;
    }

    public function setDureeDebut(?\DateTimeInterface $duree_debut): self
    {
        $this->duree_debut = $duree_debut;

        return $this;
    }

    public function getDureeFin(): ?\DateTimeInterface
    {
        return $this->duree_fin;
    }

    public function setDureeFin(?\DateTimeInterface $duree_fin): self
    {
        $this->duree_fin = $duree_fin;

        return $this;
    }

    public function getProgramme(): ?Programme
    {
        return $this->programme;
    }

    public function setProgramme(Programme $programme): self
    {
        // set the owning side of the relation if necessary
        if ($programme->getAbonnementCoach() !== $this) {
            $programme->setAbonnementCoach($this);
        }

        $this->programme = $programme;

        return $this;
    }

    /*public function generateProgram(EntityManagerInterface $entityManager): void
    {
        if ($this->statut && $this->duree_debut && $this->duree_fin) {
            // Create new program
            $program = new Programme();
            $program->setAbonnementCoach($this);
            $program->setNombreSemaines($this->calculateNumberOfWeeks());
            $entityManager->persist($program);
            $entityManager->flush();

            // Create program weeks
            $startDate = clone $this->duree_debut;
            for ($i = 0; $i < $program->getNombreSemaines(); $i++) {
                $programWeek = new ProgrammeSemaine();
                $programWeek->setProgramme($program);
                $programWeek->setNumeroSemaine($i);
                $entityManager->persist($programWeek);

            }
            $entityManager->flush();
        }
    }*/

    
}

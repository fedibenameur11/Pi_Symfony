<?php

namespace App\Entity;

use DateTime;
use App\Repository\ProgrammeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: ProgrammeRepository::class)]
class Programme
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'programme', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?AbonnementCoach $abonnementCoach = null;

    #[ORM\Column]
    private ?int $nombre_semaines = null;

    #[ORM\OneToMany(mappedBy: 'programme', targetEntity: ProgrammeSemaine::class , cascade :['persist', 'remove'])]
    private Collection $programmeSemaines;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $objectif_programme = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $niveau = null;

    public function __construct()
    {
        $this->programmeSemaines = new ArrayCollection();
    }

    public function __toString() {
        return $this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAbonnementCoach(): ?AbonnementCoach
    {
        return $this->abonnementCoach;
    }

    public function setAbonnementCoach(AbonnementCoach $abonnementCoach): self
    {
        $this->abonnementCoach = $abonnementCoach;

        return $this;
    }

    public function getNombreSemaines(): ?int
    {
        return $this->nombre_semaines;
    }

    public function setNombreSemaines(int $nombre_semaines): self
    {
        $this->nombre_semaines = $nombre_semaines;

        return $this;
    }

    /**
     * @return Collection<int, ProgrammeSemaine>
     */
    public function getProgrammeSemaines(): Collection
    {
        return $this->programmeSemaines;
    }

    public function addProgrammeSemaine(ProgrammeSemaine $programmeSemaine): self
    {
        if (!$this->programmeSemaines->contains($programmeSemaine)) {
            $this->programmeSemaines->add($programmeSemaine);
            $programmeSemaine->setProgramme($this);
        }

        return $this;
    }

    /*public function addProgrammeSemaine(ProgrammeSemaine $programWeek): self
    {
        // Set the start and end dates of the week based on the program start and end dates
        $weekNumber = $this->programmeSemaines->count() + 1;
        $weekStartDate = new \DateTime ($this->abonnementCoach->getDureeDebut()->format('Y-m-d'));
        $weekStartDate->modify('+ ' . (($weekNumber - 1) * 7) . ' days');
        $weekEndDate = new \DateTime ($weekStartDate->format('Y-m-d'));
        $weekEndDate->modify('+ 6 days');

        // Set the start and end dates of the week in the ProgramWeek entity
        $programWeek->setDureeDebutSemaine($weekStartDate);
        $programWeek->setDureeFinSemaine($weekEndDate);

        // Add the ProgramWeek to the collection
        $this->programmeSemaines[] = $programWeek;

        return $this;
    }*/

    public function removeProgrammeSemaine(ProgrammeSemaine $programmeSemaine): self
    {
        if ($this->programmeSemaines->removeElement($programmeSemaine)) {
            // set the owning side to null (unless already changed)
            if ($programmeSemaine->getProgramme() === $this) {
                $programmeSemaine->setProgramme(null);
            }
        }

        return $this;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    /*public function calculateNumberWeeks(): void
    {
        $startDate = $this->getAbonnementCoach()->getDureeDebut();
        $endDate = $this->getAbonnementCoach()->getDureeFin();

        if ($startDate && $endDate) {
            $interval = $endDate->diff($startDate);
            $this->setNombreSemaines((int) ($interval->days / 7));
        }
    }*/

    public function getObjectifProgramme(): ?string
    {
        return $this->objectif_programme;
    }

    public function setObjectifProgramme(?string $objectif_programme): self
    {
        $this->objectif_programme = $objectif_programme;

        return $this;
    }

    public function getNiveau(): ?string
    {
        return $this->niveau;
    }

    public function setNiveau(?string $niveau): self
    {
        $this->niveau = $niveau;

        return $this;
    }
}

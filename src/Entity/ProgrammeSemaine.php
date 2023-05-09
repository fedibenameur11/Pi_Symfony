<?php

namespace App\Entity;

use App\Repository\ProgrammeSemaineRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use DateTime;

#[ORM\Entity(repositoryClass: ProgrammeSemaineRepository::class)]
class ProgrammeSemaine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'programmeSemaines')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Programme $programme = null;

    #[ORM\Column]
    private ?int $numero_semaine = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    
    private ?string $nutrition_planning = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $entrainement_planning = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $objectif_semaine = null;

    public function __toString(): string
    {
        return $this->numero_semaine;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProgramme(): ?Programme
    {
        return $this->programme;
    }

    public function setProgramme(?Programme $programme): self
    {
        $this->programme = $programme;

        return $this;
    }

    public function getNumeroSemaine(): ?int
    {
        return $this->numero_semaine;
    }

    public function setNumeroSemaine(int $numero_semaine): self
    {
        $this->numero_semaine = $numero_semaine;

        return $this;
    }

    public function getNutritionPlanning(): ?string
    {
        return $this->nutrition_planning;
    }

    public function setNutritionPlanning(string $nutrition_planning): self
    {
        $this->nutrition_planning = $nutrition_planning;

        return $this;
    }

    public function getEntrainementPlanning(): ?string
    {
        return $this->entrainement_planning;
    }

    public function setEntrainementPlanning(string $entrainement_planning): self
    {
        $this->entrainement_planning = $entrainement_planning;

        return $this;
    }

    public function getObjectifSemaine(): ?string
    {
        return $this->objectif_semaine;
    }

    public function setObjectifSemaine(?string $objectif_semaine): self
    {
        $this->objectif_semaine = $objectif_semaine;

        return $this;
    }



    public function getStartAndEndDates(): array
    {
        /*$startOfWeek = $this->programme->getAbonnementCoach()->getDureeDebut();*/
        $startOfWeek = new \DateTime ($this->programme->getAbonnementCoach()->getDureeDebut()->format('Y-m-d'));
        $daysToMonday = $startOfWeek->format('N') - 1;
        $startOfWeek->modify('+'.($this->numero_semaine).' weeks')->modify("-$daysToMonday days")->format('Y-m-d');
        $endOfWeek = new \DateTime ($startOfWeek->format('Y-m-d'));
        $endOfWeek->modify('+1 week')->modify('-1 day');

        return [
            'start_date' => $startOfWeek,
            'end_date' => $endOfWeek,
        ];
    }
}

<?php

namespace App\Entity;

use App\Repository\LikeReponseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LikeReponseRepository::class)]
class LikeReponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'likeReponses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Reponse $Reponse = null;

    #[ORM\ManyToOne(inversedBy: 'likeReponses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Users $User = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReponse(): ?Reponse
    {
        return $this->Reponse;
    }

    public function setReponse(?Reponse $Reponse): self
    {
        $this->Reponse = $Reponse;

        return $this;
    }

    public function getUser(): ?Users
    {
        return $this->User;
    }

    public function setUser(?Users $User): self
    {
        $this->User = $User;

        return $this;
    }
}

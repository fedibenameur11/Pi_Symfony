<?php

namespace App\Entity;

use App\Repository\ReponseRepository;
use App\Repository\QuestionRepository;
use App\Entity\Users;
use App\Repository\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ReponseRepository::class)]
class Reponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["reponse"])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(["reponse","question"])]
    private ?string $rep = null;

    #[ORM\Column]
    #[Groups(["question","reponse"])]
    private ?\DateTimeImmutable $createdAt = null;

    #[Groups(["reponse","question"])]
    #[ORM\ManyToOne(inversedBy: 'reponses')]
    private ?Question $question = null;

    #[ORM\ManyToOne(inversedBy: 'reponses')]
    private ?Users $user = null;

    #[ORM\OneToMany(mappedBy: 'Reponse', targetEntity: LikeReponse::class)]
    private Collection $likeReponses;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable;
        $this->likeReponses = new ArrayCollection();
       
     
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRep(): ?string
    {
        return $this->rep;
    }

    public function setRep(string $rep): self
    {
        $this->rep = $rep;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, LikeReponse>
     */
    public function getLikeReponses(): Collection
    {
        return $this->likeReponses;
    }

    public function addLikeReponse(LikeReponse $likeReponse): self
    {
        if (!$this->likeReponses->contains($likeReponse)) {
            $this->likeReponses->add($likeReponse);
            $likeReponse->setReponse($this);
        }

        return $this;
    }

    public function removeLikeReponse(LikeReponse $likeReponse): self
    {
        if ($this->likeReponses->removeElement($likeReponse)) {
            // set the owning side to null (unless already changed)
            if ($likeReponse->getReponse() === $this) {
                $likeReponse->setReponse(null);
            }
        }

        return $this;
    }
     public function isLikesByUser(Users $user):bool
    {
        foreach ($this->likeReponses as $likes) {
            if ($likes->getUser() === $user) return true;
        }

        return false;
    }
}

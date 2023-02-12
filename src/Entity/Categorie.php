<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
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
    private ?string $nom = null;

    #[ORM\OneToMany(mappedBy: 'cat', targetEntity: Produit::class)]
    private Collection $prods;

    public function __construct()
    {
        $this->prods = new ArrayCollection();
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

    /**
     * @return Collection<int, Produit>
     */
    public function getProds(): Collection
    {
        return $this->prods;
    }

    public function addProd(Produit $prod): self
    {
        if (!$this->prods->contains($prod)) {
            $this->prods->add($prod);
            $prod->setCat($this);
        }

        return $this;
    }

    public function removeProd(Produit $prod): self
    {
        if ($this->prods->removeElement($prod)) {
            // set the owning side to null (unless already changed)
            if ($prod->getCat() === $this) {
                $prod->setCat(null);
            }
        }

        return $this;
    }
    public function __toString()
    {
        return $this->getNom();
    }


}

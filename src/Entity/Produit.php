<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Mailer\Mailer;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("prod")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'veuillez remplir le champ du nom')]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Il faut inserer au moins {{ limit }} characteres',
        maxMessage: 'Il faut inserer au maximum {{ limit }} characteres',
    )]
    #[Assert\Type(
        type:"string",
        message:"veuillez inserer un nom correct "
    )]
    #[Groups("prod")]
    private ?string $nom = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'veuillez remplir le champ du prix')]
    
    #[Assert\Type(
        type:"float",
        message:"veuillez inserer un nombre "
    )]
    #[Groups("prod")]
    private ?float $prix = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'veuillez remplir le champ du quantite')]
    #[Assert\Type(
        type:"integer",
        message:"veuillez inserer un nombre "
    )]
    #[Groups("prod")]
    private ?int $quantite = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'veuillez remplir le champ du poids')]
    #[Assert\Type(
        type:"float",
        message:"veuillez inserer un nombre "
    )]
    #[Groups("prod")]
    private ?float $poids = null;

    #[ORM\ManyToOne(inversedBy: 'prods')]
    #[Assert\NotBlank(message: 'veuillez choisir la categorie')]
    private ?Categorie $cat = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups("prod")]
    private ?string $imageP = null;

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

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getPoids(): ?float
    {
        return $this->poids;
    }

    public function setPoids(float $poids): self
    {
        $this->poids = $poids;

        return $this;
    }

    public function getCat(): ?Categorie
    {
        return $this->cat;
    }

    public function setCat(?Categorie $cat): self
    {
        $this->cat = $cat;

        return $this;
    }

    public function getImageP(): ?string
    {
        return $this->imageP;
    }

    public function setImageP(?string $imageP): self
    {
        $this->imageP = $imageP;

        return $this;
    }
    public function sendEmail(string $nom)
    {
        $email = (new Email())
        ->from('fanditoo@gmail.com')
        ->to('test.symf123@gmail.com')
        ->subject('produit quantite faible')
        ->text("la quantite du produit $nom est trés faible");
        $transport=new GmailSmtpTransport('test.symf123@gmail.com','akwsweycawoglxxj');
        $mailer=new Mailer($transport);
        $mailer->send($email);
    }
}

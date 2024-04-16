<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id_produit = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "le champ est vide")]
    private ?string $nom_produit = null;

    #[ORM\Column(length: 255)]
    private ?string $image_produit = null;

    #[ORM\Column(type: "float")]
    #[Assert\NotBlank(message: "le champ est vide")]
    private ?float $prix_produit = null;

    public function getId_produit(): ?int
    {
        return $this->id_produit;
    }

    public function getNom_produit(): ?string
    {
        return $this->nom_produit;
    }

    public function setNom_produit(?string $nom_produit): self
    {
        $this->nom_produit = $nom_produit;

        return $this;
    }

    public function getImage_produit(): ?string
    {
        return $this->image_produit;
    }

    public function setImage_produit(?string $image_produit): self
    {
        $this->image_produit = $image_produit;

        return $this;
    }

    public function getPrix_produit(): ?float
    {
        return $this->prix_produit;
    }

    public function setPrix_produit(?float $prix_produit): self
    {
        $this->prix_produit = $prix_produit;

        return $this;
    }
}

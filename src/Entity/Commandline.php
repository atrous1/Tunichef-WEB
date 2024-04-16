<?php

namespace App\Entity;

use App\Repository\CommandlineRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandlineRepository::class)]
class Commandline
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $idCommande = null;

    #[ORM\Column(nullable: true)]
    private ?int $idProduit = null;

    #[ORM\Column(nullable: true)]
    private ?int $quantite = null;

    #[ORM\Column(nullable: true)]
    private ?float $prixTotale = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCommande(): ?int
    {
        return $this->idCommande;
    }

    public function setIdCommande(?int $idCommande): static
    {
        $this->idCommande = $idCommande;

        return $this;
    }

    public function getIdProduit(): ?int
    {
        return $this->idProduit;
    }

    public function setIdProduit(?int $idProduit): static
    {
        $this->idProduit = $idProduit;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(?int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getPrixTotale(): ?float
    {
        return $this->prixTotale;
    }

    public function setPrixTotale(?float $prixTotale): static
    {
        $this->prixTotale = $prixTotale;

        return $this;
    }
}

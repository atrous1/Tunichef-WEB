<?php

namespace App\Entity;

use App\Repository\PromotionRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Evenement;
use App\Entity\Produit;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: PromotionRepository::class)]
class Promotion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "promotionId")]
    private ?int $promotionId = null;

    #[ORM\Column(name: "prix_promo",type: "float")]
    #[Assert\NotBlank(message: "le champ est vide")]
    #[Assert\Positive]
    private ?float $prix = null;

    #[ORM\Column(name: "promotionName", length: 255)]
    #[Assert\NotBlank(message: "le champ est vide")]
    private ?string $promotionName = null;

   

    #[ORM\ManyToOne(targetEntity: Evenement::class)]
    #[ORM\JoinColumn(name: 'event_id', referencedColumnName: 'eventId')]
    private ?Evenement $event = null;

    #[ORM\ManyToOne(targetEntity: Produit::class)]
    #[ORM\JoinColumn(name: 'produit_id', referencedColumnName: 'id_produit')]
    private ?Produit $produit = null;
    

    public function getPromotionId(): ?int
    {
        return $this->promotionId;
    }

    public function getPromotionName(): ?string
    {
        return $this->promotionName;
    }

    public function setPromotionName(?string $promotionName): self
    {
        $this->promotionName = $promotionName;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(?float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getEvent(): ?Evenement
    {
        return $this->event;
    }

    public function setEvent(?Evenement $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): self
    {
        $this->produit = $produit;

        return $this;
    }
}

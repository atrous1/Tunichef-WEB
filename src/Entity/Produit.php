<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Menu;


/**
 * Produit
 *
 * @ORM\Table(name="produit", indexes={@ORM\Index(name="fk_menu", columns={"fk_menu"})})
 * @ORM\Entity
 */
class Produit
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_produit", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public $idProduit;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_produit", type="string", length=255, nullable=false)
     */
    public $nomProduit;

    /**
     * @var string
     *
     * @ORM\Column(name="description_produit", type="string", length=255, nullable=false)
     */
    public $descriptionProduit;

    /**
     * @var string|null
     *
     * @ORM\Column(name="image_produit", type="string", length=255, nullable=true)
     */
    public $imageProduit;

    /**
     * @var float
     *
     * @ORM\Column(name="prix_produit", type="float", precision=10, scale=0, nullable=false)
     */
    public $prixProduit;

    /**
     * @var int
     *
     * @ORM\Column(name="like_produit", type="integer", nullable=false)
     */
    public $likeProduit = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="dislike_produit", type="integer", nullable=false)
     */
    public $dislikeProduit = '0';

    /**
     * @var \Menu
     *
     * @ORM\ManyToOne(targetEntity="Menu")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fk_menu", referencedColumnName="id_menu")
     * })
     */
    public $fkMenu;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $qrCode;


//complete the getters and setters for the produit
    public function getIdProduit(): ?int
    {
        return $this->idProduit;
    }

    public function getNomProduit(): ?string
    {
        return $this->nomProduit;
    }

    public function setNomProduit(string $nomProduit): self
    {
        $this->nomProduit = $nomProduit;

        return $this;
    }

    public function getDescriptionProduit(): ?string
    {
        return $this->descriptionProduit;
    }

    public function setDescriptionProduit(string $descriptionProduit): self
    {
        $this->descriptionProduit = $descriptionProduit;

        return $this;
    }

    public function getImageProduit(): ?string
    {
        return $this->imageProduit;
    }

    public function setImageProduit(?string $imageProduit): self
    {
        $this->imageProduit = $imageProduit;

        return $this;
    }

    public function getPrixProduit(): ?float
    {
        return $this->prixProduit;
    }

    public function setPrixProduit(float $prixProduit): self
    {
        $this->prixProduit = $prixProduit;

        return $this;
    }

    public function getLikeProduit(): ?int
    {
        return $this->likeProduit;
    }

    public function setLikeProduit(int $likeProduit): self
    {
        $this->likeProduit = $likeProduit;

        return $this;
    }

    public function getDislikeProduit(): ?int
    {
        return $this->dislikeProduit;
    }

    public function setDislikeProduit(int $dislikeProduit): self
    {
        $this->dislikeProduit = $dislikeProduit;

        return $this;
    }

    public function getFkMenu(): ?Menu
    {
        return $this->fkMenu;
    }

    public function setFkMenu(?Menu $fkMenu): self
    {
        $this->fkMenu = $fkMenu;

        return $this;
    }

    public function getQrCode(): ?string
    {
        return $this->qrCode;
    }

    public function setQrCode(?string $qrCode): self
    {
        $this->qrCode = $qrCode;

        return $this;
    }
}

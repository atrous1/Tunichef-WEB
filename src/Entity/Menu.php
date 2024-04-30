<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Produit;


/**
 * Menu
 *
 * @ORM\Table(name="menu")
 * @ORM\Entity
 */
class Menu
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_menu", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public $idMenu;

    /**
     * @var int
     *
     * @ORM\Column(name="nbr_page", type="integer", nullable=false)
     */
    public $nbrPage;

    /**
     * @var string
     *
     * @ORM\Column(name="categorie", type="string", length=255, nullable=false)
     */
    public $categorie;
     /**
     * @ORM\OneToMany(targetEntity="App\Entity\Produit", mappedBy="fkMenu")
     */
    public $produits;
    /**
     * @var string
     *
     * @ORM\Column(name="origine", type="string", length=255, nullable=false)
     */
    public $origine;
    /**
     * Get the value of idMenu
     *
     * @return int
     */
    public function getIdMenu()
    {
        return $this->idMenu;
    }

    /**
     * Set the value of idMenu
     *
     * @param int $idMenu
     * @return self
     */
    public function setIdMenu($idMenu)
    {
        $this->idMenu = $idMenu;
        return $this;
    }

    /**
     * Get the value of nbrPage
     *
     * @return int
     */
    public function getNbrPage()
    {
        return $this->nbrPage;
    }

    /**
     * Set the value of nbrPage
     *
     * @param int $nbrPage
     * @return self
     */
    public function setNbrPage($nbrPage)
    {
        $this->nbrPage = $nbrPage;
        return $this;
    }

    /**
     * Get the value of categorie
     *
     * @return string
     */
    public function getCategorie()
    {
        return $this->categorie;
    }

    /**
     * Set the value of categorie
     *
     * @param string $categorie
     * @return self
     */
    public function setCategorie($categorie)
    {
        $this->categorie = $categorie;
        return $this;
    }

    /**
     * Get the value of origine
     *
     * @return string
     */
    public function getOrigine()
    {
        return $this->origine;
    }

    /**
     * Set the value of origine
     *
     * @param string $origine
     * @return self
     */
    public function setOrigine($origine)
    {
        $this->origine = $origine;
        return $this;
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function __toString()
    {
        return $this->categorie;
    }
   

    public function __construct()
    {
        $this->produits = new ArrayCollection();
    }

    /**
     * @return Collection|Produit[]
     */
    public function getProduits(): Collection
    {
        return $this->produits;
    }
     /**
     * Get the image of the first product associated with this menu.
     *
     * @return string|null
     */
    public function getImageProduit(): ?string
    {
        $product = $this->produits->first();
        return $product ? $product->getImageProduit() : null;
    }

    /**
     * Get the name of the first product associated with this menu.
     *
     * @return string|null
     */
    public function getNomProduit(): ?string
    {
        $product = $this->produits->first();
        return $product ? $product->getNomProduit() : null;
    }

    /**
     * Get the description of the first product associated with this menu.
     *
     * @return string|null
     */
    public function getDescriptionProduit(): ?string
    {
        $product = $this->produits->first();
        return $product ? $product->getDescriptionProduit() : null;
    }

    /**
     * Get the price of the first product associated with this menu.
     *
     * @return float|null
     */
    public function getPrixProduit(): ?float
    {
        $product = $this->produits->first();
        return $product ? $product->getPrixProduit() : null;
    }

}

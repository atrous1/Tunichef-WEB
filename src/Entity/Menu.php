<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

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
    private $idMenu;

    /**
     * @var int
     *
     * @ORM\Column(name="nbr_page", type="integer", nullable=false)
     */
    private $nbrPage;

    /**
     * @var string
     *
     * @ORM\Column(name="categorie", type="string", length=255, nullable=false)
     */
    private $categorie;

    /**
     * @var string
     *
     * @ORM\Column(name="origine", type="string", length=255, nullable=false)
     */
    private $origine;
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

}

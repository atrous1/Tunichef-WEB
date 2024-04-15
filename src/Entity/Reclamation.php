<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=ReclamationRepository::class)
 */
class Reclamation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *  @Assert\Regex(
     *      pattern="/^[^a-z].*$/",
     *      message="La description ne peut pas commencer par une lettre minuscule."
     * )
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     *  @Assert\Range(
     *      max = 10,
     *      maxMessage = "L'avis ne peut pas dépasser 10."
     * )
     */
   
    private $avis;

    /**
     * @ORM\Column(type="date")
    
     */
    private $daterec;

    /**
     * @ORM\OneToMany(targetEntity=Reponse::class, mappedBy="idRec")
     
     */
    private  $idRep;

    public function __construct()
    {
        $this->idRep = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAvis(): ?int
    {
        return $this->avis;
    }

    public function setAvis(int $avis): self
    {
        $this->avis = $avis;

        return $this;
    }

    public function getDaterec(): ?\DateTimeInterface
    {
        return $this->daterec;
    }

    public function setDaterec(\DateTimeInterface $daterec): self
    {
        $this->daterec = $daterec;

        return $this;
    }

    /**
     * @return Collection<int, Reponse>
     */
    public function getIdRep(): Collection
    {
        return $this->idRep;
    }

    public function addIdRep(Reponse $idRep): self
    {
        if (!$this->idRep->contains($idRep)) {
            $this->idRep[] = $idRep;
            $idRep->setIdRec($this);
        }

        return $this;
    }

    public function removeIdRep(Reponse $idRep): self
    {
        if ($this->idRep->removeElement($idRep)) {
            // set the owning side to null (unless already changed)
            if ($idRep->getIdRec() === $this) {
                $idRep->setIdRec(null);
            }
        }

        return $this;
    }
}

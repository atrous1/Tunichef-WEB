<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert; 

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom_reservation = null;

    #[ORM\Column(nullable: true)]
    private ?int $num_table = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Regex(
        pattern: '/^\d{8}$/',
        message: "Le numéro de téléphone doit contenir exactement 8 chiffres."
    )]
    private ?int $num_tel = null;


    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $calendrier = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomReservation(): ?string
    {
        return $this->nom_reservation;
    }

    public function setNomReservation(?string $nom_reservation): static
    {
        $this->nom_reservation = $nom_reservation;

        return $this;
    }

    public function getNumTable(): ?int
    {
        return $this->num_table;
    }

    public function setNumTable(?int $num_table): static
    {
        $this->num_table = $num_table;

        return $this;
    }

    public function getNumTel(): ?int
    {
        return $this->num_tel;
    }

    public function setNumTel(?int $num_tel): static
    {
        $this->num_tel = $num_tel;

        return $this;
    }

    public function getCalendrier(): ?\DateTimeInterface
    {
        return $this->calendrier;
    }

    public function setCalendrier(?\DateTimeInterface $calendrier): static
    {
        $this->calendrier = $calendrier;

        return $this;
    }

    public function getIdUser(): ?int
    {
        return $this->id_user;
    }

    public function setIdUser(?int $id_user): static
    {
        $this->id_user = $id_user;

        return $this;
    }
}

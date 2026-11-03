<?php

namespace App\Entity;

use App\Repository\ComunidadRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ComunidadRepository::class)]
class Comunidad
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    private ?string $direccion = null;

    #[ORM\Column(length: 6)]
    private ?string $pin = null;

    #[ORM\ManyToOne(inversedBy: 'comunidades')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Administrador $administrador = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    public function setDireccion(string $direccion): static
    {
        $this->direccion = $direccion;

        return $this;
    }

    public function getPin(): ?string
    {
        return $this->pin;
    }

    public function setPin(string $pin): static
    {
        $this->pin = $pin;

        return $this;
    }

    public function getAdministrador(): ?Administrador
    {
        return $this->administrador;
    }

    public function setAdministrador(?Administrador $administrador): static
    {
        $this->administrador = $administrador;

        return $this;
    }
}

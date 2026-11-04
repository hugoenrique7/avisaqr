<?php

namespace App\Entity;

use App\Repository\ZonaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ZonaRepository::class)]
class Zona
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nombre = null;

    #[ORM\Column]
    private ?bool $tieneQr = true;

    #[ORM\ManyToOne(inversedBy: 'zonas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Comunidad $comunidad = null;

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

    public function isTieneQr(): ?bool
    {
        return $this->tieneQr;
    }

    public function setTieneQr(bool $tieneQr): static
    {
        $this->tieneQr = $tieneQr;

        return $this;
    }

    public function getComunidad(): ?Comunidad
    {
        return $this->comunidad;
    }

    public function setComunidad(?Comunidad $comunidad): static
    {
        $this->comunidad = $comunidad;

        return $this;
    }
}

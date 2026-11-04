<?php

namespace App\Entity;

use App\Repository\ComunidadRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @var Collection<int, Zona>
     */
    #[ORM\OneToMany(targetEntity: Zona::class, mappedBy: 'comunidad')]
    private Collection $zonas;

    /**
     * @var Collection<int, Aviso>
     */
    #[ORM\OneToMany(targetEntity: Aviso::class, mappedBy: 'comunidad')]
    private Collection $avisos;

    public function __construct()
    {
        $this->zonas = new ArrayCollection();
        $this->avisos = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Zona>
     */
    public function getZonas(): Collection
    {
        return $this->zonas;
    }

    public function addZona(Zona $zona): static
    {
        if (!$this->zonas->contains($zona)) {
            $this->zonas->add($zona);
            $zona->setComunidad($this);
        }

        return $this;
    }

    public function removeZona(Zona $zona): static
    {
        if ($this->zonas->removeElement($zona)) {
            // set the owning side to null (unless already changed)
            if ($zona->getComunidad() === $this) {
                $zona->setComunidad(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Aviso>
     */
    public function getAvisos(): Collection
    {
        return $this->avisos;
    }

    public function addAviso(Aviso $aviso): static
    {
        if (!$this->avisos->contains($aviso)) {
            $this->avisos->add($aviso);
            $aviso->setComunidad($this);
        }

        return $this;
    }

    public function removeAviso(Aviso $aviso): static
    {
        if ($this->avisos->removeElement($aviso)) {
            // set the owning side to null (unless already changed)
            if ($aviso->getComunidad() === $this) {
                $aviso->setComunidad(null);
            }
        }

        return $this;
    }
}

<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\ZonaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ZonaRepository::class)]
class Zona
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Escribe el nombre de la zona.')]
    #[Assert\Length(max: 100)]
    private ?string $nombre = null;

    #[ORM\Column]
    private ?bool $tieneQr = true;

    #[ORM\ManyToOne(inversedBy: 'zonas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Comunidad $comunidad = null;

    /**
     * @var Collection<int, Incidencia>
     */
    #[ORM\OneToMany(targetEntity: Incidencia::class, mappedBy: 'zona')]
    private Collection $incidencias;

    public function __construct()
    {
        $this->incidencias = new ArrayCollection();
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

    /**
     * @return Collection<int, Incidencia>
     */
    public function getIncidencias(): Collection
    {
        return $this->incidencias;
    }

    public function addIncidencia(Incidencia $incidencia): static
    {
        if (!$this->incidencias->contains($incidencia)) {
            $this->incidencias->add($incidencia);
            $incidencia->setZona($this);
        }

        return $this;
    }

    public function removeIncidencia(Incidencia $incidencia): static
    {
        if ($this->incidencias->removeElement($incidencia)) {
            // set the owning side to null (unless already changed)
            if ($incidencia->getZona() === $this) {
                $incidencia->setZona(null);
            }
        }

        return $this;
    }
}

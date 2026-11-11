<?php

namespace App\Entity;

use App\Enum\EstadoIncidencia;
use App\Enum\PrioridadIncidencia;
use App\Repository\IncidenciaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IncidenciaRepository::class)]
class Incidencia
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10, unique: true)]
    private ?string $codigo = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $descripcion = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $foto = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $piso = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $telefono = null;

    #[ORM\Column(enumType: EstadoIncidencia::class)]
    private ?EstadoIncidencia $estado = EstadoIncidencia::Recibida;

    #[ORM\Column(nullable: true, enumType: PrioridadIncidencia::class)]
    private ?PrioridadIncidencia $prioridad = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $fechaCreacion = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $fechaResolucion = null;

    #[ORM\Column]
    private ?int $cuantidadReabierta = 0;

    #[ORM\ManyToOne(inversedBy: 'incidencias')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Zona $zona = null;

    /**
     * @var Collection<int, Afectado>
     */
    #[ORM\OneToMany(targetEntity: Afectado::class, mappedBy: 'incidencia', orphanRemoval: true)]
    private Collection $afectados;

    public function __construct()
    {
        $this->fechaCreacion = new \DateTimeImmutable();
        $this->afectados = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(string $codigo): static
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): static
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getFoto(): ?string
    {
        return $this->foto;
    }

    public function setFoto(?string $foto): static
    {
        $this->foto = $foto;

        return $this;
    }

    public function getPiso(): ?string
    {
        return $this->piso;
    }

    public function setPiso(?string $piso): static
    {
        $this->piso = $piso;

        return $this;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(?string $telefono): static
    {
        $this->telefono = $telefono;

        return $this;
    }

    public function getEstado(): ?EstadoIncidencia
    {
        return $this->estado;
    }

    public function setEstado(EstadoIncidencia $estado): static
    {
        $this->estado = $estado;

        return $this;
    }

    public function getPrioridad(): ?PrioridadIncidencia
    {
        return $this->prioridad;
    }

    public function setPrioridad(?PrioridadIncidencia $prioridad): static
    {
        $this->prioridad = $prioridad;

        return $this;
    }

    public function getFechaCreacion(): ?\DateTimeImmutable
    {
        return $this->fechaCreacion;
    }

    public function setFechaCreacion(\DateTimeImmutable $fechaCreacion): static
    {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
    }

    public function getFechaResolucion(): ?\DateTimeImmutable
    {
        return $this->fechaResolucion;
    }

    public function setFechaResolucion(?\DateTimeImmutable $fechaResolucion): static
    {
        $this->fechaResolucion = $fechaResolucion;

        return $this;
    }
    public function getCuantidadReabierta(): ?int
    {
        return $this->cuantidadReabierta;
    }

    public function setCuantidadReabierta(int $cuantidadReabierta): static
    {
        $this->cuantidadReabierta = $cuantidadReabierta;

        return $this;
    }

    public function getZona(): ?Zona
    {
        return $this->zona;
    }

    public function setZona(?Zona $zona): static
    {
        $this->zona = $zona;

        return $this;
    }

    /**
     * @return Collection<int, Afectado>
     */
    public function getAfectados(): Collection
    {
        return $this->afectados;
    }

    public function addAfectado(Afectado $afectado): static
    {
        if (!$this->afectados->contains($afectado)) {
            $this->afectados->add($afectado);
            $afectado->setIncidencia($this);
        }

        return $this;
    }

    public function removeAfectado(Afectado $afectado): static
    {
        if ($this->afectados->removeElement($afectado)) {
            // set the owning side to null (unless already changed)
            if ($afectado->getIncidencia() === $this) {
                $afectado->setIncidencia(null);
            }
        }

        return $this;
    }
}

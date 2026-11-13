<?php

namespace App\Entity;

use App\Repository\AvisoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AvisoRepository::class)]
class Aviso
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank(message: 'Escribe un titulo.')]
    #[Assert\Length(max: 150)]
    private ?string $titulo = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Escribe el texto del aviso')]
    private ?string $texto = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $fecha = null;

    #[ORM\ManyToOne(inversedBy: 'avisos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Comunidad $comunidad = null;

    public function __construct()
    {
        $this->fecha = new \DateTimeImmutable();
    }
    public function getId(): ?int
    {
        return $this->id;
    }


    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): static
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getTexto(): ?string
    {
        return $this->texto;
    }

    public function setTexto(string $texto): static
    {
        $this->texto = $texto;

        return $this;
    }

    public function getFecha(): ?\DateTimeImmutable
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeImmutable $fecha): static
    {
        $this->fecha = $fecha;

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

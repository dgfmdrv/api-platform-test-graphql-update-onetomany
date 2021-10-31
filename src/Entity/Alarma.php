<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ApiResource(
 *      itemOperations={
 *          "GET"={
 *              "normalization_context"={
 *                  "groups"={"get_alarma"}
 *              }
 *          },
 *          "PUT"={
 *              "normalization_context"={
 *                  "groups"={"put_alarma"}
 *              },
 *              "denormalization_context"={
 *                  "groups"={"put_alarma"}
 *              }
 *          },
 *     },
 *     collectionOperations={
 *          "GET"={
 *              "normalization_context"={
 *                  "groups"={"get_alarma"}
 *              }
 *          },
 *          "POST"={
 *              "normalization_context"={
 *                  "groups"={"post_alarma"}
 *              },
 *              "denormalization_context"={
 *                  "groups"={"post_alarma"}
 *              }
 *          }
 *     },
 *     attributes={
 *          "pagination_enabled"=false,
 *          "order"={"fechaInicio": "DESC"}
 *     },
 * )
 */
class Alarma
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"read", "car:write"})
     */
    private $id;
    
    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"get_alarma", "post_alarma", "put_alarma", "post_alarma"})
     */
    private $descripcion;
    
    /**
     * @var \Datetime 
     *
     * @ORM\Column(type="datetime")
     * @Assert\NotNull
     * @Groups({"get_alarma", "post_alarma", "put_alarma", "get_planta_estado"})
     */
    private $fechaInicio;
    
    /**
     * @var \Datetime 
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"get_alarma", "post_alarma", "put_alarma", "get_planta_estado"})
     */
    private $fechaEdicion;
    
    /**
     * @var Evento[]
     *
     * @ORM\OneToMany(targetEntity="Evento", mappedBy="alarma", cascade={"persist"})
     * @ORM\OrderBy({"fechaInicio" = "ASC"})
     * @ApiSubresource
     * @Groups({"post_alarma", "put_alarma"})
     */
    private $evento;
    
    ############

    public function __construct()
    {
        $this->evento = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getFechaInicio(): ?\DateTimeInterface
    {
        return $this->fechaInicio;
    }

    public function setFechaInicio(\DateTimeInterface $fechaInicio): self
    {
        $this->fechaInicio = $fechaInicio;

        return $this;
    }

    public function getFechaEdicion(): ?\DateTimeInterface
    {
        return $this->fechaEdicion;
    }

    public function setFechaEdicion(?\DateTimeInterface $fechaEdicion): self
    {
        $this->fechaEdicion = $fechaEdicion;

        return $this;
    }


    /**
     * @return Collection|Evento[]
     */
    public function getEvento(): Collection
    {
        return $this->evento;
    }

    public function addEvento(Evento $evento): self
    {
        if (!$this->evento->contains($evento)) {
            $this->evento[] = $evento;
            $evento->setAlarma($this);
        }

        return $this;
    }

    public function removeEvento(Evento $evento): self
    {
        if ($this->evento->contains($evento)) {
            $this->evento->removeElement($evento);
            // set the owning side to null (unless already changed)
            if ($evento->getAlarma() === $this) {
                $evento->setAlarma(null);
            }
        }

        return $this;
    }


}
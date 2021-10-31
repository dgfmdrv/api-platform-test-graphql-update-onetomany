<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Entity
 * @ApiResource(
 *      itemOperations={
 *          "GET"={
 *              "method"="GET",
 *              "normalization_context"={
 *                  "groups"={"get_evento"}
 *              }
 *          },
 *          "patch"={
 *              "method"="PATCH",
 *              "normalization_context"={
 *                  "groups"={"patch_evento"}
 *              },
 *              "denormalization_context"={
 *                  "groups"={"patch_evento"}
 *              }
 *          }
 *      },
 *      collectionOperations={
 *          "GET"={
 *              "method"="GET",
 *              "normalization_context"={
 *                  "groups"={"get_evento"}
 *              }
 *          },
 *          "POST"={
 *              "method"="POST",
 *              "normalization_context"={
 *                  "groups"={"post_evento"}
 *              },
 *              "denormalization_context"={
 *                  "groups"={"post_evento"}
 *              }
 *          },
 *      },
 *      subresourceOperations={
 *          "api_alarmas_eventos_get_subresource"={
 *              "method"="GET",
 *              "normalization_context"={
 *                  "groups"={"get_alarma"}
 *              }
 *          },
 *      },
 *      attributes={
 *          "order"={"fechaInicio": "DESC"},
 *          "pagination_enabled"=false,
 *      },
 * )
 */
class Evento
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
     * @ORM\Column(type="string", length=150)
     * @Assert\NotNull
     * @Groups({"get_evento", "post_evento", "get_alarma", "post_evento", "post_alarma"})
     */
    private $motivo;
        
    /**
     * @var \Datetime
     *
     * @ORM\Column(type="datetime")
     * @Assert\NotNull
     * @Groups({"get_evento", "post_evento", "get_alarma", "post_alarma"})
     */
    private $fechaInicio;
    
    /**
     * @var \Datetime
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"get_evento", "post_evento", "get_alarma", "post_alarma", "patch_evento"})
     */
    private $fechaFin;
    
    /**
     * @var Alarma
     *
     * @ORM\ManyToOne(targetEntity="Alarma", inversedBy="evento")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Groups({"get_evento", "post_evento"})
     */
    private $alarma;

    public function getId(): ?string
    {
        return $this->id;
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

    public function getFechaFin(): ?\DateTimeInterface
    {
        return $this->fechaFin;
    }

    public function setFechaFin(?\DateTimeInterface $fechaFin): self
    {
        $this->fechaFin = $fechaFin;

        return $this;
    }

    public function getMotivo(): ?string
    {
        return $this->motivo;
    }

    public function setMotivo(string $motivo): self
    {
        $this->motivo = $motivo;

        return $this;
    }

    public function getAlarma(): ?Alarma
    {
        return $this->alarma;
    }

    public function setAlarma(?Alarma $alarma): self
    {
        $this->alarma = $alarma;

        return $this;
    }



}
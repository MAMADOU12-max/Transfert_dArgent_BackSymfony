<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CommissionsRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Entity(repositoryClass=CommissionsRepository::class)
 * @ApiFilter(SearchFilter::class, properties={"Archivage":"exact","type":"exact"})
 * @ApiFilter(SearchFilter::class, properties={"active":"exact","type":"exact"})
 * @ApiResource(
  *     attributes={"security"="is_granted('ROLE_ADMINSYSTEM')"}
 * )
 */
class Commissions
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"allCommission:read"})
     */
    private $ttc;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"allCommission:read"})
     */
    private $fraisEtat;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"allCommission:read"})
     */
    private $fraisSystem;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"allCommission:read"})
     */
    private $fraisEnvoie;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"allCommission:read"})
     */
    private $fraisRetrait;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"allCommission:read"})
     */
    private $active;

    /**
     * @ORM\Column(type="boolean")
     */
    private $Archivage;

    public function __construct() {
        $this->Archivage = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTtc(): ?int
    {
        return $this->ttc;
    }

    public function setTtc(int $ttc): self
    {
        $this->ttc = $ttc;

        return $this;
    }

    public function getFraisEtat(): ?int
    {
        return $this->fraisEtat;
    }

    public function setFraisEtat(int $fraisEtat): self
    {
        $this->fraisEtat = $fraisEtat;

        return $this;
    }

    public function getFraisSystem(): ?int
    {
        return $this->fraisSystem;
    }

    public function setFraisSystem(int $fraisSystem): self
    {
        $this->fraisSystem = $fraisSystem;

        return $this;
    }

    public function getFraisEnvoie(): ?int
    {
        return $this->fraisEnvoie;
    }

    public function setFraisEnvoie(int $fraisEnvoie): self
    {
        $this->fraisEnvoie = $fraisEnvoie;

        return $this;
    }

    public function getFraisRetrait(): ?int
    {
        return $this->fraisRetrait;
    }

    public function setFraisRetrait(int $fraisRetrait): self
    {
        $this->fraisRetrait = $fraisRetrait;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getArchivage(): ?bool
    {
        return $this->Archivage;
    }

    public function setArchivage(bool $Archivage): self
    {
        $this->Archivage = $Archivage;

        return $this;
    }
}

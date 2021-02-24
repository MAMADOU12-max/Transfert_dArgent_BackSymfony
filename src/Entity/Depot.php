<?php

namespace App\Entity;

use App\Repository\DepotRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DepotRepository::class)
 */
class Depot
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $dateDepot;

    /**
     * @ORM\Column(type="integer")
     */
    private $montantDeDepot;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="depots")
     */
    private $caissiers;

    /**
     * @ORM\ManyToOne(targetEntity=Compte::class, inversedBy="depots")
     */
    private $comptes;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDepot(): ?\DateTimeInterface
    {
        return $this->dateDepot;
    }

    public function setDateDepot(\DateTimeInterface $dateDepot): self
    {
        $this->dateDepot = $dateDepot;

        return $this;
    }

    public function getMontantDeDepot(): ?int
    {
        return $this->montantDeDepot;
    }

    public function setMontantDeDepot(int $montantDeDepot): self
    {
        $this->montantDeDepot = $montantDeDepot;

        return $this;
    }

    public function getCaissiers(): ?User
    {
        return $this->caissiers;
    }

    public function setCaissiers(?User $caissiers): self
    {
        $this->caissiers = $caissiers;

        return $this;
    }

    public function getComptes(): ?Compte
    {
        return $this->comptes;
    }

    public function setComptes(?Compte $comptes): self
    {
        $this->comptes = $comptes;

        return $this;
    }
}

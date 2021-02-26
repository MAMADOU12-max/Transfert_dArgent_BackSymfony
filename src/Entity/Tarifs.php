<?php

namespace App\Entity;

use App\Repository\TarifsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TarifsRepository::class)
 */
class Tarifs
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $Borne_Inferieur;

    /**
     * @ORM\Column(type="integer")
     */
    private $Borne_Superieur;

    /**
     * @ORM\Column(type="integer")
     */
    private $Frais;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBorneInferieur(): ?int
    {
        return $this->Borne_Inferieur;
    }

    public function setBorneInferieur(int $Borne_Inferieur): self
    {
        $this->Borne_Inferieur = $Borne_Inferieur;

        return $this;
    }

    public function getBorneSuperieur(): ?int
    {
        return $this->Borne_Superieur;
    }

    public function setBorneSuperieur(int $Borne_Superieur): self
    {
        $this->Borne_Superieur = $Borne_Superieur;

        return $this;
    }

    public function getFrais(): ?int
    {
        return $this->Frais;
    }

    public function setFrais(int $Frais): self
    {
        $this->Frais = $Frais;

        return $this;
    }
}

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\TransactionRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;

/**
 * @ORM\Entity(repositoryClass=TransactionRepository::class)
 * @ApiResource(
*     collectionOperations={
 *          "doTransaction"={
 *              "route_name"="doTransaction" ,
 *                "normalization_context"={"groups"={"send:write"}}
 *           }
 *     },
*)
 */
class Transaction
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
    private $montant;

    /**
     * @ORM\Column(type="date")
     */
    private $dateDepot;

    /**
     * @ORM\Column(type="date")
     */
    private $dateRetrait;

    /**
     * @ORM\Column(type="date")
     */
    private $dateAnnulation;

    /**
     * @ORM\Column(type="integer")
     */
    private $TTC;

    /**
     * @ORM\Column(type="integer")
     */
    private $fraisEtat;

    /**
     * @ORM\Column(type="integer")
     */
    private $fraisSystem;

    /**
     * @ORM\Column(type="integer")
     */
    private $fraisEnvoie;

    /**
     * @ORM\Column(type="integer")
     */
    private $fraisRetrait;

    /**
     * @ORM\Column(type="integer")
     */
    private $codeTransaction;

    /**
     * @ORM\ManyToOne(targetEntity=Compte::class, inversedBy="transactions")
     * @ApiSubresource()
     */
    private $comptes;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="transactions")
     */
    private $retrait;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="transactions")
     */
    private $deposer;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="transactions")
     */
    private $recuperer;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="transactions")
     */
    private $envoyer;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(int $montant): self
    {
        $this->montant = $montant;

        return $this;
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

    public function getDateRetrait(): ?\DateTimeInterface
    {
        return $this->dateRetrait;
    }

    public function setDateRetrait(\DateTimeInterface $dateRetrait): self
    {
        $this->dateRetrait = $dateRetrait;

        return $this;
    }

    public function getDateAnnulation(): ?\DateTimeInterface
    {
        return $this->dateAnnulation;
    }

    public function setDateAnnulation(\DateTimeInterface $dateAnnulation): self
    {
        $this->dateAnnulation = $dateAnnulation;

        return $this;
    }

    public function getTTC(): ?int
    {
        return $this->TTC;
    }

    public function setTTC(int $TTC): self
    {
        $this->TTC = $TTC;

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

    public function getCodeTransaction(): ?int
    {
        return $this->codeTransaction;
    }

    public function setCodeTransaction(int $codeTransaction): self
    {
        $this->codeTransaction = $codeTransaction;

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

    public function getRetrait(): ?User
    {
        return $this->retrait;
    }

    public function setRetrait(?User $retrait): self
    {
        $this->retrait = $retrait;

        return $this;
    }

    public function getDeposer(): ?User
    {
        return $this->deposer;
    }

    public function setDeposer(?User $deposer): self
    {
        $this->deposer = $deposer;

        return $this;
    }

    public function getRecuperer(): ?Client
    {
        return $this->recuperer;
    }

    public function setRecuperer(?Client $recuperer): self
    {
        $this->recuperer = $recuperer;

        return $this;
    }

    public function getEnvoyer(): ?Client
    {
        return $this->envoyer;
    }

    public function setEnvoyer(?Client $envoyer): self
    {
        $this->envoyer = $envoyer;

        return $this;
    }
}

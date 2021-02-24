<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CompteRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CompteRepository::class)
 * @ApiResource(
 *      itemOperations={
*            "getComptebyId"={
*                  "path"="/comptes/{id}" ,
*                 "method"="GET"
*            }
*     }
 * )
 */
class Compte
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"allTransaction:read","getTransactionById:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"allTransaction:read","getTransactionById:read"})
     */
    private $numCompte;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"allTransaction:read","getTransactionById:read"})
     */
    private $solde;

    /**
     * @ORM\OneToMany(targetEntity=Depot::class, mappedBy="comptes")
     */
    private $depots;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="comptes")
     */
    private $adminSystem;

    /**
     * @ORM\OneToOne(targetEntity=Agence::class, cascade={"persist", "remove"})
     */
    private $agence;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="comptes")
     */
    private $transactions;


    public function __construct()
    {
        $this->depots = new ArrayCollection();
        $this->adminSystem = new ArrayCollection();
        $this->transactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumCompte(): ?int
    {
        return $this->numCompte;
    }

    public function setNumCompte(int $numCompte): self
    {
        $this->numCompte = $numCompte;

        return $this;
    }

    public function getSolde(): ?int
    {
        return $this->solde;
    }

    public function setSolde(int $solde): self
    {
        $this->solde = $solde;

        return $this;
    }

    /**
     * @return Collection|Depot[]
     */
    public function getDepots(): Collection
    {
        return $this->depots;
    }

    public function addDepot(Depot $depot): self
    {
        if (!$this->depots->contains($depot)) {
            $this->depots[] = $depot;
            $depot->setComptes($this);
        }

        return $this;
    }

    public function removeDepot(Depot $depot): self
    {
        if ($this->depots->removeElement($depot)) {
            // set the owning side to null (unless already changed)
            if ($depot->getComptes() === $this) {
                $depot->setComptes(null);
            }
        }

        return $this;
    }

    public function getAdminSystem(): ?User
    {
        return $this->adminSystem;
    }

    public function setAdminSystem(?User $adminSystem): self
    {
        $this->adminSystem = $adminSystem;

        return $this;
    }

    public function getAgence(): ?Agence
    {
        return $this->agence;
    }

    public function setAgence(?Agence $agence): self
    {
        $this->agence = $agence;

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setComptes($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getComptes() === $this) {
                $transaction->setComptes(null);
            }
        }

        return $this;
    }
}

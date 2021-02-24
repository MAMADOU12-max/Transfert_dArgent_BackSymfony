<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ClientRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ClientRepository::class)
 * @ApiResource(
 *    itemOperations={
 *           "getClientbyId"={
 *            "path"="/clients/{id}" ,
 *            "method"="GET"
 *        }
 *    }
 * )
 */
class Client
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
      * @Groups({"allTransaction:read","getTransactionById:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
      * @Groups({"allTransaction:read","getTransactionById:read"})
     */
    private $nomComplet;

    /**
     * @ORM\Column(type="integer", nullable=true)
      * @Groups({"allTransaction:read","getTransactionById:read"})
     */
    private $phone;

    /**
     * @ORM\Column(type="integer")
      * @Groups({"allTransaction:read","getTransactionById:read"})
     */
    private $identityNumber;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="recuperer")
     */
    private $transactions;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="envoyer")
     */
    private $transaction;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
        $this->transaction = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomComplet(): ?string
    {
        return $this->nomComplet;
    }

    public function setNomComplet(string $nomComplet): self
    {
        $this->nomComplet = $nomComplet;

        return $this;
    }

    public function getPhone(): ?int
    {
        return $this->phone;
    }

    public function setPhone(?int $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getIdentityNumber(): ?int
    {
        return $this->identityNumber;
    }

    public function setIdentityNumber(int $identityNumber): self
    {
        $this->identityNumber = $identityNumber;

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
            $transaction->setRecuperer($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getRecuperer() === $this) {
                $transaction->setRecuperer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransaction(): Collection
    {
        return $this->transaction;
    }
}

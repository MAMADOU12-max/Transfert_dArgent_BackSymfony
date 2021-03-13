<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CompteRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=CompteRepository::class)
 * @ApiResource(
 *     collectionOperations={
 *           "addCompteByAdminSystem"={
 *                "path"="/compte" ,
 *                "method"="POST" ,
 *                "denormalization_context"={"groups"={"comtpe:read"}} ,
 *                "security_post_denormalize"="is_granted('ROLE_ADMINAGENCE') || is_granted('ROLE_ADMINSYSTEM')",
 *                "security_message"="Only admin system can create an account" 
 *           },
  *          "getAllCompte"={
 *               "path"="/comptes" ,
 *               "method"="GET" ,
 *                 "security_post_denormalize"="is_granted('ROLE_ADMINAGENCE') || is_granted('ROLE_ADMINSYSTEM')",
 *                "security_message"="Only admin system can see accounts" 
 *           }
 *     },
 *    itemOperations={
 *          "getComptebyId"={
 *               "path"="/comptes/{id}" ,
 *               "method"="GET" ,
 *                "security_post_denormalize"="is_granted('ROLE_ADMINAGENCE') || is_granted('ROLE_ADMINSYSTEM')",
 *                "security_message"="Only admin system can see a a count" 
 *           },
 *           "bloquerCompte"={
 *               "path"="/comptes/{id}" ,
 *               "method"="DELETE" ,
 *                "security_post_denormalize"="is_granted('ROLE_ADMINAGENCE') || is_granted('ROLE_ADMINSYSTEM')" ,
 *                "security_message"="Only admin system can block an account" 
 *          },
 *           "editCompte"={
 *               "path"="/comptes/{id}" ,
 *               "method"="PUT" ,
 *                "security_post_denormalize"="is_granted('ROLE_ADMINAGENCE') || is_granted('ROLE_ADMINSYSTEM')" ,
 *                "security_message"="Only admin system can update an account" 
 *           
 *          }
 *    }
 * )
 */


class Compte
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"allTransaction:read","getTransactionById:read","depot:read","getDepotById:read","partAgencebyId:read",
     * "getAgencebyId:read","usersById:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"allTransaction:read","getTransactionById:read","depot:read","getDepotById:read"
     * ,"comtpe:read","agence:create","allagence:read","getAgencebyId:read"})
      * @Assert\NotBlank
     * @Assert\GreaterThanOrEqual(
     *     value = 700000
     * )
     */
    private $solde;

    /**
     * @ORM\OneToMany(targetEntity=Depot::class, mappedBy="comptes")
     */
    private $depots;
     
    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="comptes")
     * @ApiSubresource
     * @Groups({"comtpe:read"})
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="compteEnvoie")
     * @Groups({"partAgencebyId:read"})
     */
    private $transactions;

    /**
     * @ORM\Column(type="integer", unique=true)
       * @Assert\NotBlank
     * @Groups({"allTransaction:read","getTransactionById:read","depot:read","getDepotById:read",
     * "comtpe:read","agence:create","getAgencebyId:read"})
     */
    private $identifiantCompte;

    /**
     * @ORM\Column(type="boolean")
     */
    private $disabled;

    /**
     * @ORM\OneToOne(targetEntity=Agence::class, cascade={"persist", "remove"})
     * @Groups({"comtpe:read"})
    * @Assert\NotBlank
    * @ApiSubresource
     */
    private $agence;

    public function __construct()
    {
        $this->depots = new ArrayCollection();
        $this->adminSystem = new ArrayCollection();
        $this->transactions = new ArrayCollection();
        $this->disabled = false;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUsers(): ?User
    {
        return $this->users;
    }

    public function setUsers(?User $users): self
    {
        $this->users = $users;

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
            $transaction->setCompteEnvoie($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getCompteEnvoie() === $this) {
                $transaction->setCompteEnvoie(null);
            }
        }

        return $this;
    }

    public function getIdentifiantCompte(): ?int
    {
        return $this->identifiantCompte;
    }

    public function setIdentifiantCompte(int $identifiantCompte): self
    {
        $this->identifiantCompte = $identifiantCompte;

        return $this;
    }

    public function getDisabled(): ?bool
    {
        return $this->disabled;
    }

    public function setDisabled(bool $disabled): self
    {
        $this->disabled = $disabled;

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
}

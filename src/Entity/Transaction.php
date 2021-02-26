<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\TransactionRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TransactionRepository::class)
 *  @ApiFilter(SearchFilter::class, properties={"Etat":"exact"})
 * @ApiResource(
 *     collectionOperations={
 *          "doTransaction"={
 *                "route_name"="doTransaction" ,
 *                "method"="POST",
 *                   "deserialize"= false
 *           } ,
 *          "recupTransaction"={
 *                "route_name"="recupTransaction" ,
 *                "method"="PUT",
 *                   "deserialize"= false
 *           },
 *           "getAllTransaction"={
 *                  "path"="/transactions" ,
*                   "method"="GET" ,
*                   "normalization_context"={"groups"={"allTransaction:read"}} ,
 *          }
 *     },
 *    itemOperations={
 *       "getTransactionById"={
 *                  "path"="/transactions/{id}" ,
*                   "method"="GET" ,
*                   "normalization_context"={"groups"={"getTransactionById:read"}} ,
 *        }
 *   }
*)
 */
class Transaction
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
    private $montant;

    /**
     * @ORM\Column(type="date")
     * @Groups({"allTransaction:read","getTransactionById:read"})
     * @Assert\Date
     * @var string A "Y-m-d" formatted value
     */
    private $dateDepot;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"allTransaction:read","getTransactionById:read"})
     * * @Assert\Date
     * @var string A "Y-m-d" formatted value
     */
    private $dateRetrait;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"allTransaction:read","getTransactionById:read"})
     */
    private $dateAnnulation;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"allTransaction:read","getTransactionById:read"})
     */
    private $TTC;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"allTransaction:read","getTransactionById:read"})
     */
    private $fraisEtat;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"allTransaction:read","getTransactionById:read"})
     */
    private $fraisSystem;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"allTransaction:read","getTransactionById:read"})
     */
    private $fraisEnvoie;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"allTransaction:read","getTransactionById:read"})
     */
    private $fraisRetrait;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"allTransaction:read","getTransactionById:read"})
     */
    private $codeTransaction;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="transactions")
     * @Groups({"allTransaction:read","getTransactionById:read"})
     */
    private $retrait;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="transactions")
     * @Groups({"allTransaction:read","getTransactionById:read"})
     */
    private $deposer;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="transactions")
     * @Groups({"allTransaction:read","getTransactionById:read"})
     */
    private $recuperer;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="transaction")
     * @Groups({"allTransaction:read","getTransactionById:read"})
     */
    private $envoyer;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Etat;

    /**
     * @ORM\ManyToOne(targetEntity=Compte::class, inversedBy="transactions")
     */
    private $compteEnvoie;

    /**
     * @ORM\ManyToOne(targetEntity=Compte::class, inversedBy="transactions")
     */
    private $compteRetrait;

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

    public function getCodeTransaction(): ?string
    {
        return $this->codeTransaction;
    }

    public function setCodeTransaction(string $codeTransaction): self
    {
        $this->codeTransaction = $codeTransaction;

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

    public function getEtat(): ?string
    {
        return $this->Etat;
    }

    public function setEtat(string $Etat): self
    {
        $this->Etat = $Etat;

        return $this;
    }

    public function getCompteEnvoie(): ?Compte
    {
        return $this->compteEnvoie;
    }

    public function setCompteEnvoie(?Compte $compteEnvoie): self
    {
        $this->compteEnvoie = $compteEnvoie;

        return $this;
    }

    public function getCompteRetrait(): ?Compte
    {
        return $this->compteRetrait;
    }

    public function setCompteRetrait(?Compte $compteRetrait): self
    {
        $this->compteRetrait = $compteRetrait;

        return $this;
    }
}

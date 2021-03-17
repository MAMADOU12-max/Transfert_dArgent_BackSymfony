<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\AgenceRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use ApiPlatform\Core\Annotation\ApiFilter;

/**
 * @ORM\Entity(repositoryClass=AgenceRepository::class)
 * @ApiFilter(SearchFilter::class, properties={"disabled":"exact"})
 * @ApiResource(
 *     collectionOperations={
 *         "createAgence"={
 *              "path"="/agence" ,
 *              "method"="POST" ,
 *              "security_post_denormalize"="is_granted('ROLE_ADMINSYSTEM')" ,
 *              "security_message"="Only admin system can create an agence" 
 *          }, 
 *           "allAgence"={
 *              "path"="/agences" ,
 *              "method"="GET" ,
 *              "normalization_context"={"groups"={"allagence:read"}} ,
 *              "security_post_denormalize"="is_granted('ROLE_ADMINSYSTEM')" ,
 *              "security_message"="Only admin system can see list" 
 *          }
 *     },
 *     itemOperations={
 *         "getAgenceById"={
 *             "path"="/agence/{id}", 
 *              "method"="GET" ,
 *              "normalization_context"={"groups"={"getAgencebyId:read"}} ,
 *              "security_post_denormalize"="is_granted('ROLE_ADMINSYSTEM')" ,
 *             "security_message"="Only admin system can see detail" 
 *         },
 *        "deleteAgenceById"={
 *              "path"="/agence/{id}", 
 *              "method"="DELETE" ,
 *              "security_post_denormalize"="is_granted('ROLE_ADMINSYSTEM')" ,
 *              "security_message"="Only admin system can block an agence" 
 *         },
 *         "updateAgence"={
 *              "path"="/agence/{id}", 
 *              "method"="PUT" ,
 *              "security_post_denormalize"="is_granted('ROLE_ADMINSYSTEM')" ,
 *              "security_message"="Only admin system can block an agence" 
 *         },
 *          "partByAgence"={
 *              "path"="/agence/transaction/{id}/part", 
 *              "normalization_context"={"groups"={"partAgencebyId:read"}} ,
 *              "method"="GET" ,
 *              "security_post_denormalize"="is_granted('ROLE_ADMINSYSTEM')" ,
 *              "security_message"="Only admin system can block an agence"        
 *         }
 *    }
 * )
 */
class Agence
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"allagence:read","getAgencebyId:read","usersById:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"agence:create","allagence:read","getAgencebyId:read","partAgencebyId:read"})
     * @Assert\NotBlank
     */
    private $nomAgence;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"agence:create","allagence:read","getAgencebyId:read"})
     * @Assert\NotBlank
     */
    private $adressAgence;

    /**
     * @ORM\Column(type="boolean")
     */
    private $disabled;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="agence")
     * @Groups({"allagence:read","getAgencebyId:read"})
     * @ApiSubresource
     */
    private $users;

    /**
     * @ORM\OneToOne(targetEntity=Compte::class, cascade={"persist", "remove"})
     */
    private $compte;


    public function __construct()
    {
        $this->users = new ArrayCollection(); 
        $this->disabled = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomAgence(): ?string
    {
        return $this->nomAgence;
    }

    public function setNomAgence(string $nomAgence): self
    {
        $this->nomAgence = $nomAgence;

        return $this;
    }

    public function getAdressAgence(): ?string
    {
        return $this->adressAgence;
    }

    public function setAdressAgence(string $adressAgence): self
    {
        $this->adressAgence = $adressAgence;

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

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setAgence($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getAgence() === $this) {
                $user->setAgence(null);
            }
        }

        return $this;
    }

    public function getCompte(): ?Compte
    {
        return $this->compte;
    }

    public function setCompte(?Compte $compte): self
    {
        $this->compte = $compte;

        return $this;
    }
}

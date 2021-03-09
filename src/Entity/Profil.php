<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProfilRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use ApiPlatform\Core\Annotation\ApiFilter;


/**
 * @ORM\Entity(repositoryClass=ProfilRepository::class)
 * @ApiFilter(SearchFilter::class, properties={"archivage":"exact"})
 * @ApiResource(
 *    collectionOperations={
 *         "createProfil"={
 *              "path"="/profil" ,
 *              "method"="POST" ,
 *              "security_post_denormalize"="is_granted('ROLE_ADMINSYSTEM')" ,
 *              "security_message"="Only admin system can create a profil" 
 *          }, 
 *           "allProfils"={
 *              "path"="/profils" ,
 *              "method"="GET" ,
 *              "normalization_context"={"groups"={"allProfil:read"}} ,
 *              "security_post_denormalize"="is_granted('ROLE_ADMINSYSTEM')" ,
 *              "security_message"="Only admin system can see list" 
 *          }
 *     },
 *     itemOperations={
 *         "getProfilById"={
 *             "path"="/profil/{id}", 
 *              "method"="GET" ,
 *              "normalization_context"={"groups"={"getProfilbyId:read"}} ,
 *              "security_post_denormalize"="is_granted('ROLE_ADMINSYSTEM')" ,
 *             "security_message"="Only admin system can see detail" 
 *         },
 *        "putProfilById"={
 *             "path"="/profil/{id}", 
 *              "method"="PUT" ,
 *              "normalization_context"={"groups"={"getProfilbyId:read"}} ,
 *              "security_post_denormalize"="is_granted('ROLE_ADMINSYSTEM')" ,
 *             "security_message"="Only admin system can update profil" 
 *         },
 *        "deleteProfilById"={
 *              "path"="/profil/{id}", 
 *              "method"="DELETE" ,
 *              "security_post_denormalize"="is_granted('ROLE_ADMINSYSTEM')" ,
 *              "security_message"="Only admin system can block an agence" 
 *         }
 *    }
 * )
 */
class Profil
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"users:read","allProfil:read","getProfilbyId:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"users:read","allProfil:read","getProfilbyId:read"})
     */
    private $libelle;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="profils")
     */
    private $users;

    /**
     * @ORM\Column(type="boolean")
     */
    private $archivage;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->archivage = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

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
            $user->setProfils($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getProfils() === $this) {
                $user->setProfils(null);
            }
        }

        return $this;
    }

    public function getArchivage(): ?bool
    {
        return $this->archivage;
    }

    public function setArchivage(bool $archivage): self
    {
        $this->archivage = $archivage;

        return $this;
    }
}

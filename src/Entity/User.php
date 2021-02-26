<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 *  @ApiFilter(SearchFilter::class, properties={"archivage":"exact","type":"exact"})
* @ApiResource(
*     collectionOperations={
 *          "adding"={
 *              "route_name"="addUser" ,
 *               "deserialize"= false ,
 *              "security_post_denormalize"="is_granted('ROLE_ADMINAGENCE') || is_granted('ROLE_ADMINSYSTEM')" ,
 *                "security_message"="Only admin system and admin agence can do this action" 
 *           } ,
 *           "getAllUsers"={
 *                "path"="/admin/users" ,
 *                "method"="GET" ,
 *                "normalization_context"={"groups"={"users:read"}},
 *                "security_post_denormalize"="is_granted('ROLE_ADMINAGENCE') || is_granted('ROLE_ADMINSYSTEM')" ,
 *                "security_message"="Only admin system and admin agence can do this action" ,
 *           }
 *     },
 *     itemOperations={
*               "getUserbyId"={
*                  "path"="/admin/user/{id}" ,
*                   "security_message"="Only admins can add users." ,
*                   "method"="GET",
*                   "normalization_context"={"groups"={"usersById:read"}}
*              },
*             "bloquerUserbyId"={
*                  "path"="/admin/user/{id}" ,
*                   "security_post_denormalize"="is_granted('ROLE_ADMINAGENCE') || is_granted('ROLE_ADMINSYSTEM')" ,    
*                   "security_message"="Only admin agence and admin system can add users." ,
*                   "method"="DELETE"
*              }
*     }
* )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"users:read","usersById:read","depotbycaissier:read","depot:read","getDepotById:read","getAgencebyId:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"users:read","usersById:read","depot:read","getDepotById:read","allagence:read","getAgencebyId:read"})
     */
    private $username;
 
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"users:read","usersById:read","depot:read","getDepotById:read","allagence:read","getAgencebyId:read"})
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"users:read","usersById:read","depot:read","getDepotById:read","allagence:read","getAgencebyId:read"})
     */
    private $lastname;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"users:read","usersById:read","depot:read","getDepotById:read","allagence:read","getAgencebyId:read"})
     */
    private $phone;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"users:read","usersById:read","depot:read","getDepotById:read","allagence:read","getAgencebyId:read"})
     */
    private $identityNum;

    /**
     * @Groups({"users:read","usersById:read","depot:read","getDepotById:read","allagence:read","getAgencebyId:read"})
     * @ORM\Column(type="blob", nullable=true)
     */
    private $avatar;

    /**
     * @Groups({"users:read","usersById:read","depot:read","getDepotById:read","allagence:read","getAgencebyId:read"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="boolean")
       * @Groups({"users:read","usersById:read","depot:read","getDepotById:read"})
     */
    private $archivage;

    /**
     * @ORM\OneToMany(targetEntity=Depot::class, mappedBy="caissiers")
     */
    private $depots;

    /**
     * @Groups({"users:read","usersById:read"})
     * @ORM\ManyToOne(targetEntity=Profil::class, inversedBy="users")
     */
    private $profils;

    /**
     * @Groups({"users:read","usersById:read","depot:read","getDepotById:read","allagence:read","getAgencebyId:read"})
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity=Compte::class, mappedBy="users")
     */
    private $comptes;

    /**
     * @ORM\ManyToOne(targetEntity=Agence::class, inversedBy="users")
     */
    private $agence;

    public function __construct()
    {
        $this->depots = new ArrayCollection();
        $this->comptes = new ArrayCollection();
        $this->Archivage = false ;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_'.$this->profils->getLibelle();
        // $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

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

    public function getIdentityNum(): ?int
    {
        return $this->identityNum;
    }

    public function setIdentityNum(int $identityNum): self
    {
        $this->identityNum = $identityNum;

        return $this;
    }

    public function getAvatar()
    {
        $avatar = $this->avatar;
        if($avatar) {
            return (base64_encode(stream_get_contents($this->avatar))) ; 
         }
        return $avatar;
    }

    public function setAvatar($avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

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
            $depot->setCaissiers($this);
        }

        return $this;
    }

    public function removeDepot(Depot $depot): self
    {
        if ($this->depots->removeElement($depot)) {
            // set the owning side to null (unless already changed)
            if ($depot->getCaissiers() === $this) {
                $depot->setCaissiers(null);
            }
        }

        return $this;
    }

    public function getProfils(): ?Profil
    {
        return $this->profils;
    }

    public function setProfils(?Profil $profils): self
    {
        $this->profils = $profils;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->profils->getLibelle();
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection|Compte[]
     */
    public function getComptes(): Collection
    {
        return $this->comptes;
    }

    public function addCompte(Compte $compte): self
    {
        if (!$this->comptes->contains($compte)) {
            $this->comptes[] = $compte;
            $compte->setUsers($this);
        }

        return $this;
    }

    public function removeCompte(Compte $compte): self
    {
        if ($this->comptes->removeElement($compte)) {
            // set the owning side to null (unless already changed)
            if ($compte->getUsers() === $this) {
                $compte->setUsers(null);
            }
        }

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

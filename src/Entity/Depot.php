<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\DepotRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Annotation\ApiFilter;

/**
 * @ORM\Entity(repositoryClass=DepotRepository::class)
 * @ApiFilter(SearchFilter::class, properties={"Archivage":"exact","type":"exact"})
 * @ApiResource(
 *     collectionOperations={
 *          "getAllDepot"={
 *               "path"="/caissiers" ,
 *               "method"="GET",
 *               "normalization_context"={"groups"={"depot:read"}} ,
 *               "security_post_denormalize"="is_granted('ROLE_CAISSIER') || is_granted('ROLE_ADMINSYSTEM')" ,
 *               "security_message"="Only admin system and caissier can do this action" ,
 *           },
 *           "depotByCaissier"= {
 *               
 *               "security_post_denormalize"="is_granted('ROLE_CAISSIER') || is_granted('ROLE_ADMINSYSTEM')" ,
 *               "security_message"="Only admin system and caissier can do this action", 
 *               "path"="/caissier/depot" ,
 *                "normalization_context"={"groups"={"depotbyCaissier:read"}} ,
 *               "method": "POST"
 *         } 
 *     },
 *     itemOperations={
*            "getDepotById"={
*                  "path"="/caissiers/{id}" ,
*                   "method"="GET",
*                   "normalization_context"={"groups"={"getDepotById:read"}},
*                   "security_post_denormalize"="is_granted('ROLE_CAISSIER') || is_granted('ROLE_ADMINSYSTEM')" ,
*                  "security_message"="Only admin system and caissier can do this action" 
*            },
*          "deleteDepotById"={
*                  "path"="/caissiers/{id}" ,
*                   "method"="DELETE",
*                   "security_post_denormalize"="is_granted('ROLE_ADMINSYSTEM')" ,
*                  "security_message"="Only admin system can do delete" 
*            }   
*     }
 * )
 */


class Depot
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"depot:read","getDepotById:read"})
     */
    private $id;

    /**
    * @var \DateTime
     * @ORM\Column(type="date")
     * @Groups({"depot:read","getDepotById:read","depotbyCaissier:read"})
     */
    private $dateDepot;

    /**
     * @Groups({"depot:read","getDepotById:read","depotbyCaissier:read"})
     * @ORM\Column(type="integer")
     */
    private $montantDeDepot;

    /**
    * @ApiSubresource
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="depots",cascade={"persist"})
     * @Groups({"depot:read","getDepotById:read","depotbyCaissier:read"})
     */
    private $caissiers;

    /**
     * @Groups({"depot:read","getDepotById:read","depotbyCaissier:read"})
     * @ORM\ManyToOne(targetEntity=Compte::class, inversedBy="depots",cascade={"persist"})
     * @ApiSubresource
     */
    private $comptes;

    /**
     * @ORM\Column(type="boolean")
     */
    private $Archivage;


    public function __construct()
    {
        $this->dateDepot = new \DateTime();
        $this->Archivage = false;
    }
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

    public function getArchivage(): ?bool
    {
        return $this->Archivage;
    }

    public function setArchivage(bool $Archivage): self
    {
        $this->Archivage = $Archivage;

        return $this;
    }
}

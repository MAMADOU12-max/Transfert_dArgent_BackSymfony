<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\TarifsRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TarifsRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"tarif:read"}} ,
 *      collectionOperations={     
 *         "newTarif"={
 *              "path"="/tarif" ,
 *              "method"="POST" ,
 *              "security_post_denormalize"="is_granted('ROLE_ADMINSYSTEM')" ,
 *              "security_message"="Only admin system can create a new tarif" ,
 *          }, 
 *           "allTarifs"={
 *              "path"="/tarifs" ,
 *              "method"="GET" ,
 *              "security_post_denormalize"="is_granted('ROLE_ADMINSYSTEM')" ,
 *              "security_message"="Only admin system can see list" 
 *          }
 *     },
 *     itemOperations={
 *         "editTarif"={
 *             "path"="/tarif/{id}", 
 *              "method"="PUT" ,
 *              "security_post_denormalize"="is_granted('ROLE_ADMINSYSTEM')" ,
 *             "security_message"="Only admin system can see detail" 
 *         },
 *        "deleteTarif"={
 *              "path"="/tarif/{id}", 
 *              "method"="DELETE" ,
 *              "security_post_denormalize"="is_granted('ROLE_ADMINSYSTEM')" ,
 *              "security_message"="Only admin system can delete a tarif" 
 *         },
 *          "getTarifbyId"={
 *              "path"="/tarif/{id}",
 *              "method"="GET" ,
 *              "security_post_denormalize"="is_granted('ROLE_ADMINSYSTEM')" ,
 *              "security_message"="Only admin system can block an agence"        
 *         }
 *     }
 * )
 */
class Tarifs
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"tarif:read"})
     */
    private $id;

    /**
     * @Groups({"tarif:read"})
     * @ORM\Column(type="integer")
     */
    private $Borne_Inferieur;

    /**
     * @Groups({"tarif:read"})
     * @ORM\Column(type="integer")
     */
    private $Borne_Superieur;

    /**
     * @Groups({"tarif:read"})
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

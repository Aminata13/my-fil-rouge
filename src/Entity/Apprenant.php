<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ApprenantRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ApprenantRepository::class)
 * @ApiResource(
 *  collectionOperations={
 *      "get"={
 *          "normalization_context"={"groups"={"user:read"}},
 *          "access_control"="is_granted('ROLE_FORMATEUR') || is_granted('ROLE_CM')"
 *      },
 *      "post"={
 *          "controller"=ApprenantController::class,
 *          "route_name"="add_apprenant"
 *     }
 *  },
 *  subresourceOperations={
 *      "api_profil_sorties_apprenants_get_subresource"={
 *          "access_control"="(is_granted('ROLE_ADMIN'))"
 *      }
 *  },
 *  itemOperations={
 *      "get"={
 *          "security"="is_granted('APPRENANT_VIEW', object)",
 *          "security_message"="Vous n'avez pas accès à ces informations."
 *      },
 *      "put"={
 *          "method"="PUT",
 *          "controller"=ApprenantController::class,
 *          "route_name"="edit_apprenant",
 *          "security"="is_granted('APPRENANT_EDIT', object)",
 *          "security_message"="Vous n'avez pas accès à ces informations."
 *      }
 *  },
 *  normalizationContext={"groups"={"user:read"}}
 * )
 * @UniqueEntity(
 *  fields={"username"},
 *  message="Ce login existe déjà."
 * )
 */
class Apprenant extends User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"user:read","profilSortie:read"})
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity=ProfilSortie::class, inversedBy="apprenants")
     */
    private $profilSortie;

    /**
     * @ORM\ManyToOne(targetEntity=Promotion::class, inversedBy="apprenants")
     */
    private $promotion;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProfilSortie(): ?ProfilSortie
    {
        return $this->profilSortie;
    }

    public function setProfilSortie(?ProfilSortie $profilSortie): self
    {
        $this->profilSortie = $profilSortie;

        return $this;
    }

    public function getPromotion(): ?Promotion
    {
        return $this->promotion;
    }

    public function setPromotion(?Promotion $promotion): self
    {
        $this->promotion = $promotion;

        return $this;
    }
}

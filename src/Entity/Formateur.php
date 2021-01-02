<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\FormateurRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=FormateurRepository::class)
 * @ApiResource(
 *  collectionOperations={
 *      "get"={
 *          "normalization_context"={"groups"={"user:read"}},
 *          "access_control"="(is_granted('ROLE_CM'))"
 *      },
 *      "post"={
 *         "path"="/formateurs",
 *         "route_name"="add_formateur"
 *     }
 *  },
 *  itemOperations={
 *      "get"={
 *          "security"="is_granted('FORMATEUR_VIEW', object)",
 *          "security_message"="Vous n'avez pas accès à ces informations."
 *      },
 *      "put"={
 *          "method"="PUT",
 *          "controller"=FormateurController::class,
 *          "route_name"="edit_formateur",
 *          "security"="is_granted('FORMATEUR_EDIT', object)",
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
class Formateur extends User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"user:read"})
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity=Promotion::class, mappedBy="formateurs")
     */
    private $promotions;

    public function __construct()
    {
        $this->promotions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Promotion[]
     */
    public function getPromotions(): Collection
    {
        return $this->promotions;
    }

    public function addPromotion(Promotion $promotion): self
    {
        if (!$this->promotions->contains($promotion)) {
            $this->promotions[] = $promotion;
            $promotion->addFormateur($this);
        }

        return $this;
    }

    public function removePromotion(Promotion $promotion): self
    {
        if ($this->promotions->removeElement($promotion)) {
            $promotion->removeFormateur($this);
        }

        return $this;
    }
}

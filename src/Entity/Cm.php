<?php

namespace App\Entity;

use App\Repository\CmRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=CmRepository::class)
 * @ApiResource(
 *  collectionOperations={
 *      "get"={
 *          "normalization_context"={"groups"={"user:read"}},
 *          "access_control"="(is_granted('ROLE_ADMIN'))"
 *      },
 *      "post"={
 *         "path"="/cms",
 *         "route_name"="add_cm"
 *     }
 *  },
 *  itemOperations={
 *      "get"={
 *          "security"="is_granted('CM_VIEW', object)",
 *          "security_message"="Vous n'avez pas accès à ces informations."
 *      },
 *      "put"={
 *          "method"="PUT",
 *          "controller"=CmController::class,
 *          "route_name"="edit_cm",
 *          "security"="is_granted('CM_EDIT', object)",
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
class Cm extends User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"user:read"})
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le cni est obligatoire.")
     * @Groups({"user:read"})
     */
    private $cni;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCni(): ?string
    {
        return $this->cni;
    }

    public function setCni(string $cni): self
    {
        $this->cni = $cni;

        return $this;
    }
}

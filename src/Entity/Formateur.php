<?php

namespace App\Entity;

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

    public function getId(): ?int
    {
        return $this->id;
    }
}

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\GroupeTagRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=GroupeTagRepository::class)
 * @UniqueEntity(
 * fields={"libelle"},
 * message="Ce groupe de tags existe déjà."
 * )
 * @ApiFilter(BooleanFilter::class, properties={"deleted"})
 * @ApiResource(
 *  denormalizationContext={"groups"={"groupe_tag:write"}},
 *  normalizationContext={"groups"={"groupe_tag:read"}},
 *  attributes={
 *      "security"="is_granted('ROLE_ADMIN') or is_granted('ROLE_FORMATEUR')",
 *      "security_message"="Vous n'avez pas accès à cette ressource.",
 *      "pagination_items_per_page"=5
 *  },
 *  routePrefix="/admin",
 *  collectionOperations={
 *      "get",
 *      "post_groupe_tag"={
 *         "method"="POST",
 *         "path"="/admin/groupe_tags",
 *         "controller"=GroupeTagController::class,
 *         "route_name"="add_groupe_tag"
 *     }
 *  },
 *  itemOperations={
 *      "get",
 *      "put_groupe_tag"={
 *         "method"="PUT",
 *         "path"="/admin/groupe_tags/{id}",
 *         "controller"=GroupeTagController::class,
 *         "route_name"="edit_groupe_tag"
 *     },
 *      "delete"
 *  }
 * )
 */
class GroupeTag
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"tag:read""groupe_tag:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le libelle est obligatoire.")
     * @Groups({"tag:read","groupe_tag:write","groupe_tag:read"})
     */
    private $libelle;

    /**
     * @ORM\Column(type="boolean")
     */
    private $deleted=false;

    /**
     * @ORM\ManyToMany(targetEntity=Tag::class, inversedBy="groupeTags", cascade={"persist"})
     * @ApiSubresource
     * @Assert\Valid
     * @Assert\Count(
     *      min = 1,
     *      minMessage="Au moins un  tag est requis."
     * )
     * @Groups({"groupe_tag:write","groupe_tag:read"})
     */
    private $tags;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
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

    public function getDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $this->tags->removeElement($tag);

        return $this;
    }
}

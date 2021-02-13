<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\GroupeCompetenceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=GroupeCompetenceRepository::class)
 *  @UniqueEntity(
 * fields={"libelle"},
 * message="Ce groupe de compétence existe déjà."
 * )
 * @ApiResource(
 *  routePrefix="/admin",
 *  denormalizationContext={"groups"={"grpcompetence:write"}},
 *  normalizationContext={"groups"={"grpcompetence:read_all"}},
 *  collectionOperations={
 *      "get"={
 *          "security"="(is_granted('ROLE_ADMIN') or is_granted('ROLE_FORMATEUR') or is_granted('ROLE_CM'))",
 *          "normalization_context"={"groups"={"grpcompetence:read"}}
 *      },
 *      "getByCompetences"={
 *          "method"="GET",
 *          "path"="/groupe_competences/competences",
 *          "access_control"="(is_granted('ROLE_ADMIN'))"
 *      },
 *      "post"
 *  },
 *  itemOperations={
 *      "get"={
 *          "access_control"="(is_granted('ROLE_ADMIN') or is_granted('ROLE_FORMATEUR') or is_granted('ROLE_CM'))",
 *          "normalization_context"={"groups"={"grpcompetence:read"}}
 *      },
 *      "getByIdCompetence"={
 *          "method"="GET",
 *          "path"="/groupe_competences/{id}/competences",
 *          "security"="(is_granted('ROLE_ADMIN') or is_granted('ROLE_FORMATEUR') or is_granted('ROLE_CM'))"
 *      },
 *      "put",
 *      "delete"
 *  }
 * )
 */
class GroupeCompetence
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"competence:read_all","grpcompetence:read","grpcompetence:read_all","referentiel:read","referentiel:read_all"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le libelle est obligatoire.")
     * @Groups({"competence:read_all","grpcompetence:read","grpcompetence:read_all","grpcompetence:write","referentiel:read","referentiel:read_all","referentiel:write"})
     */
    private $libelle;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Une description est requise.")
     * @Groups({"grpcompetence:read","grpcompetence:read_all","grpcompetence:write","referentiel:read","referentiel:read_all"})
     */
    private $description;

    /**
     * @ORM\Column(type="boolean")
     */
    private $deleted=false;

    /**
     * @ORM\ManyToMany(targetEntity=Competence::class, inversedBy="groupeCompetences", cascade={"persist"})
     * @Assert\Count(
     *      min = 1,
     *      minMessage="Au moins une compétence est requise."
     * )
     * @Groups({"grpcompetence:read","grpcompetence:read_all","grpcompetence:write","referentiel:read_all"})
     */
    private $competences;

    /**
     * @ORM\ManyToMany(targetEntity=Referentiel::class, mappedBy="groupeCompetences")
     */
    private $referentiels;

    public function __construct()
    {
        $this->competences = new ArrayCollection();
        $this->referentiels = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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
     * @return Collection|Competence[]
     */
    public function getCompetences(): Collection
    {
        return $this->competences;
    }

    public function addCompetence(Competence $competence): self
    {
        if (!$this->competences->contains($competence)) {
            $this->competences[] = $competence;
        }

        return $this;
    }

    public function removeCompetence(Competence $competence): self
    {
        $this->competences->removeElement($competence);

        return $this;
    }

    /**
     * @return Collection|Referentiel[]
     */
    public function getReferentiels(): Collection
    {
        return $this->referentiels;
    }

    public function addReferentiel(Referentiel $referentiel): self
    {
        if (!$this->referentiels->contains($referentiel)) {
            $this->referentiels[] = $referentiel;
            $referentiel->addGroupeCompetence($this);
        }

        return $this;
    }

    public function removeReferentiel(Referentiel $referentiel): self
    {
        if ($this->referentiels->removeElement($referentiel)) {
            $referentiel->removeGroupeCompetence($this);
        }

        return $this;
    }
}

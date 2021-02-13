<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CompetenceRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=CompetenceRepository::class)
 * @UniqueEntity(
 * fields={"libelle"},
 * message="Cette compétence existe déjà."
 * )
 * @ApiFilter(BooleanFilter::class, properties={"deleted"})
 * @ApiResource(
 *  denormalizationContext={"groups"={"competence:write"}},
 *  normalizationContext={"groups"={"competence:read_all"}},
 *  routePrefix="/admin",
 *  collectionOperations={
 *      "get"={
 *          "security"="is_granted('ROLE_ADMIN') or is_granted('ROLE_FORMATEUR') or is_granted('ROLE_CM')"
 *      },
 *      "post"
 *  },
 *  itemOperations={
 *      "get"={
 *          "security"="is_granted('ROLE_ADMIN') or is_granted('ROLE_FORMATEUR') or is_granted('ROLE_CM')"
 *      },
 *      "put",
 *      "delete"
 *  }
 * )
 */
class Competence
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"competence:read_all", "grpcompetence:read", "grpcompetence:read_all", "referentiel:read_all"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le libelle est obligatoire.")
     * @Groups({"competence:read_all", "competence:write", "grpcompetence:write", "grpcompetence:read", "grpcompetence:read_all", "referentiel:read_all"})
     */
    private $libelle;

    /**
     * @ORM\Column(type="boolean")
     */
    private $deleted=false;

    /**
     * @ORM\ManyToMany(targetEntity=GroupeCompetence::class, mappedBy="competences")
     * @Assert\Count(
     *      min = 1,
     *      minMessage="Le groupe de compétences est obligatoire."
     * )
     * @Groups({"competence:read_all","competence:write"})
     */
    private $groupeCompetences;

    /**
     * @ORM\OneToMany(targetEntity=NiveauEvaluation::class, mappedBy="competence", orphanRemoval=true, cascade={"persist"})
     * @Assert\Valid
     * @Assert\Count(
     *      min = 3,
     *      max = 3,
     *      exactMessage="Les niveaux d'évaluation doivent être exactement au nombre de 3."
     * )
     * @Groups({"competence:read_all", "competence:write", "grpcompetence:read_all"})
     */
    private $niveauEvaluations;

    public function __construct()
    {
        $this->groupeCompetences = new ArrayCollection();
        $this->niveauEvaluations = new ArrayCollection();
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
     * @return Collection|GroupeCompetence[]
     */
    public function getGroupeCompetences(): Collection
    {
        return $this->groupeCompetences;
    }

    public function addGroupeCompetence(GroupeCompetence $groupeCompetence): self
    {
        if (!$this->groupeCompetences->contains($groupeCompetence)) {
            $this->groupeCompetences[] = $groupeCompetence;
            $groupeCompetence->addCompetence($this);
        }

        return $this;
    }

    public function removeGroupeCompetence(GroupeCompetence $groupeCompetence): self
    {
        if ($this->groupeCompetences->removeElement($groupeCompetence)) {
            $groupeCompetence->removeCompetence($this);
        }

        return $this;
    }

    /**
     * @return Collection|NiveauEvaluation[]
     */
    public function getNiveauEvaluations(): Collection
    {
        return $this->niveauEvaluations;
    }

    public function addNiveauEvaluation(NiveauEvaluation $niveauEvaluation): self
    {
        if (!$this->niveauEvaluations->contains($niveauEvaluation)) {
            $this->niveauEvaluations[] = $niveauEvaluation;
            $niveauEvaluation->setCompetence($this);
        }

        return $this;
    }

    public function removeNiveauEvaluation(NiveauEvaluation $niveauEvaluation): self
    {
        if ($this->niveauEvaluations->removeElement($niveauEvaluation)) {
            // set the owning side to null (unless already changed)
            if ($niveauEvaluation->getCompetence() === $this) {
                $niveauEvaluation->setCompetence(null);
            }
        }

        return $this;
    }
}

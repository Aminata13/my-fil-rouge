<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Controller\ReferentielController;
use App\Repository\ReferentielRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ReferentielRepository::class)
 * @UniqueEntity(
 * fields={"libelle"},
 * message="Ce référentiel existe déjà."
 * )
 * @ApiResource(
 *  routePrefix="/admin",
 *  denormalizationContext={"groups"={"referentiel:write"}},
 *  normalizationContext={"groups"={"referentiel:read_all"}},
 *  collectionOperations={
 *      "get"={
 *          "security"="is_granted('ROLE_FORMATEUR')",
 *          "normalization_context"={"groups"={"referentiel:read"}}
 *      },
 *      "get_all"={
 *          "method"="GET",
 *          "path"="/referentiels/groupe_competences",
 *          "security"="is_granted('ROLE_CM')"
 *      },
 *      "post_referentiel"={
 *         "method"="POST",
 *         "route_name"="add_referentiel",
 *         "path"="/referentiels",
 *         "controller"=ReferentielController::class
 *     }
 *  },
 *  itemOperations={
 *      "get"={
 *          "security"="is_granted('ROLE_CM')",
 *          "normalization_context"={"groups"={"referentiel:read"}}
 *      },
 *      "get_competences"={
 *          "method"="GET",
 *          "path"="/referentiels/{idReferentiel}/groupe_competences/{idGroupeComp}",
 *          "controller"=ReferentielController::class,
 *          "route_name"="show_competences_by_referentiel"
 *      },
 *      "put_referentiel"={
 *         "method"="PUT",
 *         "path"="/referentiels/{id}",
 *         "controller"=ReferentielController::class,
 *         "route_name"="edit_referentiel"
 *     },
 *      "delete"={"security"="is_granted('ROLE_ADMIN')"}
 *  }
 * )
 */
class Referentiel
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"referentiel:read","referentiel:read_all"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le libelle est obligatoire.")
     * @Groups({"referentiel:read","referentiel:read_all","referentiel:write"})
     */
    private $libelle;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="La présentation est obligatoire.")
     * @Groups({"referentiel:read","referentiel:read_all","referentiel:write"})
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=CritereAdmission::class, mappedBy="referentiel", orphanRemoval=true, cascade={"persist"})
     * @Assert\Count(
     *      min = 1,
     *      minMessage="Au moins un critère d'admission est requis."
     * )
     * @Groups({"referentiel:read","referentiel:read_all"})
     */
    private $critereAdmissions;

    /**
     * @ORM\OneToMany(targetEntity=CritereEvaluation::class, mappedBy="referentiel", orphanRemoval=true, cascade={"persist"})
     * @Assert\Count(
     *      min = 1,
     *      minMessage="Au moins un critère d'évaluation est requis."
     * )
     * @Groups({"referentiel:read","referentiel:read_all"})
     */
    private $critereEvaluations;

    /**
     * @ORM\ManyToMany(targetEntity=GroupeCompetence::class, inversedBy="referentiels")
     * @Assert\Count(
     *      min = 1,
     *      minMessage="Au moins un critère d'admission est requis."
     * )
     * @Groups({"referentiel:read","referentiel:read_all"})
     * @ApiSubresource
     */
    private $groupeCompetences;

    /**
     * @ORM\Column(type="blob")
     * @Assert\File(
     *     maxSize = "1024k",
     *     mimeTypes = {"application/pdf", "application/x-pdf"},
     *     mimeTypesMessage = "Please upload a valid PDF",
     *     uploadNoFileErrorMessage="Test"
     * )
     * @Groups({"referentiel:read","referentiel:read_all"})
     */
    private $programme;

    /**
     * @ORM\Column(type="boolean")
     */
    private $deleted=false;

    /**
     * @ORM\ManyToMany(targetEntity=Promotion::class, mappedBy="referentiels")
     */
    private $promotions;

    public function __construct()
    {
        $this->critereAdmissions = new ArrayCollection();
        $this->critereEvaluations = new ArrayCollection();
        $this->groupeCompetences = new ArrayCollection();
        $this->promotions = new ArrayCollection();
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

    /**
     * @return Collection|CritereAdmission[]
     */
    public function getCritereAdmissions(): Collection
    {
        return $this->critereAdmissions;
    }

    public function addCritereAdmission(CritereAdmission $critereAdmission): self
    {
        if (!$this->critereAdmissions->contains($critereAdmission)) {
            $this->critereAdmissions[] = $critereAdmission;
            $critereAdmission->setReferentiel($this);
        }

        return $this;
    }

    public function removeCritereAdmission(CritereAdmission $critereAdmission): self
    {
        if ($this->critereAdmissions->removeElement($critereAdmission)) {
            // set the owning side to null (unless already changed)
            if ($critereAdmission->getReferentiel() === $this) {
                $critereAdmission->setReferentiel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CritereEvaluation[]
     */
    public function getCritereEvaluations(): Collection
    {
        return $this->critereEvaluations;
    }

    public function addCritereEvaluation(CritereEvaluation $critereEvaluation): self
    {
        if (!$this->critereEvaluations->contains($critereEvaluation)) {
            $this->critereEvaluations[] = $critereEvaluation;
            $critereEvaluation->setReferentiel($this);
        }

        return $this;
    }

    public function removeCritereEvaluation(CritereEvaluation $critereEvaluation): self
    {
        if ($this->critereEvaluations->removeElement($critereEvaluation)) {
            // set the owning side to null (unless already changed)
            if ($critereEvaluation->getReferentiel() === $this) {
                $critereEvaluation->setReferentiel(null);
            }
        }

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
        }

        return $this;
    }

    public function removeGroupeCompetence(GroupeCompetence $groupeCompetence): self
    {
        $this->groupeCompetences->removeElement($groupeCompetence);

        return $this;
    }

    public function getProgramme()
    {
        return base64_encode(stream_get_contents($this->programme));
    }

    public function setProgramme($programme): self
    {
        $this->programme = $programme;

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
            $promotion->addReferentiel($this);
        }

        return $this;
    }

    public function removePromotion(Promotion $promotion): self
    {
        if ($this->promotions->removeElement($promotion)) {
            $promotion->removeReferentiel($this);
        }

        return $this;
    }
}

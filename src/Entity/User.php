<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"user" = "User", "admin" = "Admin", "apprenant" = "Apprenant", "formateur"="Formateur", "cm"="Cm"})
 * @ApiResource(iri="http://schema.org/Users",
 *  collectionOperations={
 *      "get"={
 *          "path"="/admin/users",
 *          "normalization_context"={"groups"={"user:read"}},
 *          "access_control"="(is_granted('ROLE_ADMIN'))"
 *      }
 *  },
 *  itemOperations={
 *      "get"={
 *          "path"="/admin/users/{id}",
 *          "access_control"="(is_granted('ROLE_ADMIN'))"
 *      },  
 *      "delete"={
 *          "access_control"="(is_granted('ROLE_ADMIN'))"
 *      }
 *  }
 * ),
 * @ApiFilter(SearchFilter::class, properties={"profil.libelle"})
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"user:read"})
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(message="Le nom d'utilisateur est obligatoire.")
     * @Groups({"user:read"})
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Le mot de passe est obligatoire.")
     */
    private $password;

    /**
     * @ORM\ManyToOne(targetEntity=UserProfil::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"user:read"})
     */
    private $profil;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le nom est obligatoire.")
     * @Groups({"user:read"})
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le prénom est obligatoire.")
     * @Groups({"user:read"})
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(message="L'adresse mail est invalide")
     * @Groups({"user:read"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="L'adresse est obligatoire.")
     * @Groups({"user:read"})
     */
    private $address;

    /**
     * @ORM\Column(type="blob", nullable=true)
     * @Assert\NotBlank(message="L'avatar est obligatoire.")
     * @Groups({"user:read"})
     */
    private $avatar;

    /**
     * @ORM\Column(type="boolean")
     */
    private $deleted=false;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_'.strtoupper($this->profil->getLibelle());

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getProfil(): ?UserProfil
    {
        return $this->profil;
    }

    public function setProfil(?UserProfil $profil): self
    {
        $this->profil = $profil;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getAvatar()
    {
        return $this->avatar!=null?stream_get_contents($this->avatar):null;
    }

    public function setAvatar($avatar): self
    {
        $this->avatar = base64_encode($avatar);

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
}

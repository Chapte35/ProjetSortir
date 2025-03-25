<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'Il y a déja un compte avec cet email')]
#[UniqueEntity(fields: ['pseudo'], message: 'Il y a déja un compte avec ce pseudo')]
class Participant implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(message: "Email obligatoire !")]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
//    #[Assert\NotBlank(message: "Mot de passe obligatoire !")]
//    #[Assert\Length(min: 6, max: 255, minMessage: "Mot de passe trop court (min 6)", maxMessage: "Mot de passe trop long (max 255)")]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 2, max: 255, minMessage: "Nom trop court (min 2)", maxMessage: "Nom trop long (max 255)")]
    #[Assert\NotBlank(message: "Nom obligatoire !")]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 2, max: 255, minMessage: "Prenom trop court (min 2)", maxMessage: "Prenom trop long (max 255)")]
    #[Assert\NotBlank(message: "Prenom obligatoire !")]
    private ?string $prenom = null;

    #[ORM\Column]
    private ?bool $actif = null;

    #[ORM\Column(length: 12)]
    #[Assert\Length(min: 10, max: 12, minMessage: "Numéro trop court", maxMessage: "Numéro trop long")]
    #[Assert\NotBlank(message: "Telephone obligatoire !")]
    private ?string $telephone = null;

    #[ORM\ManyToOne(inversedBy: 'participants')]
    #[Assert\NotBlank(message: "Vous devez être rattaché à un site !")]
    private ?Site $site = null;

    /**
     * @var Collection<int, Sortie>
     */
    #[ORM\ManyToMany(targetEntity: Sortie::class, inversedBy: 'participants')]
    private Collection $mesInscriptions;

    /**
     * @var Collection<int, Sortie>
     */
    #[ORM\OneToMany(targetEntity: Sortie::class, mappedBy: 'organisateur', orphanRemoval: true)]
    private Collection $sortiesCree;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Pseudo obligatoire !")]
    #[Assert\Length(min: 3, max: 255, minMessage: "Mot de passe trop court (min 3)", maxMessage: "Mot de passe trop long (max 255)")]
    private ?string $pseudo = null;

    public function __construct()
    {
        $this->mesInscriptions = new ArrayCollection();
        $this->sortiesCree = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): static
    {
        $this->actif = $actif;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): static
    {
        $this->site = $site;

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getMesInscriptions(): Collection
    {
        return $this->mesInscriptions;
    }

    public function addMesInscription(Sortie $mesInscription): static
    {
        if (!$this->mesInscriptions->contains($mesInscription)) {
            $this->mesInscriptions->add($mesInscription);
        }

        return $this;
    }

    public function removeMesInscription(Sortie $mesInscription): static
    {
        $this->mesInscriptions->removeElement($mesInscription);

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSortiesCree(): Collection
    {
        return $this->sortiesCree;
    }

    public function addSortiesCree(Sortie $sortiesCree): static
    {
        if (!$this->sortiesCree->contains($sortiesCree)) {
            $this->sortiesCree->add($sortiesCree);
            $sortiesCree->setOrganisateur($this);
        }

        return $this;
    }

    public function removeSortiesCree(Sortie $sortiesCree): static
    {
        if ($this->sortiesCree->removeElement($sortiesCree)) {
            // set the owning side to null (unless already changed)
            if ($sortiesCree->getOrganisateur() === $this) {
                $sortiesCree->setOrganisateur(null);
            }
        }

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }
}

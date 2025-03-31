<?php

namespace App\Entity;

use App\Repository\GroupePriveRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupePriveRepository::class)]
class GroupePrive
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 51)]
    private ?string $nom = null;

    /**
     * @var Collection<int, Participant>
     */
    #[ORM\ManyToMany(targetEntity: Participant::class, inversedBy: 'groupesPrives')]
    private Collection $membres;

    #[ORM\ManyToOne(inversedBy: 'mesGroupes')]
    private ?Participant $proprio = null;

    public function __construct()
    {
        $this->membres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection<int, Participant>
     */
    public function getMembres(): Collection
    {
        return $this->membres;
    }

    public function addMembre(Participant $membre): static
    {
        if (!$this->membres->contains($membre)) {
            $this->membres->add($membre);
        }

        return $this;
    }

    public function removeMembre(Participant $membre): static
    {
        $this->membres->removeElement($membre);

        return $this;
    }

    public function getProprio(): ?Participant
    {
        return $this->proprio;
    }

    public function setProprio(?Participant $proprio): static
    {
        $this->proprio = $proprio;

        return $this;
    }
}

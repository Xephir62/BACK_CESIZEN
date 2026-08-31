<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\ExerciceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ExerciceRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(security: "is_granted('ROLE_ADMIN')"),
        new Put(security: "is_granted('ROLE_ADMIN')"),
        new Delete(security: "is_granted('ROLE_ADMIN')"),
    ],
    normalizationContext: ['groups' => ['exercice:read']],
    denormalizationContext: ['groups' => ['exercice:write']]
)]
class Exercice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['exercice:read', 'utilisateurs:read', 'commentaire:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['exercice:read', 'exercice:write', 'utilisateurs:read', 'commentaire:read'])]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['exercice:read', 'exercice:write', 'utilisateurs:read'])]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['exercice:read', 'exercice:write', 'utilisateurs:read'])]
    private ?int $inspirationSeconde = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['exercice:read', 'exercice:write', 'utilisateurs:read'])]
    private ?int $respirationSeconde = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['exercice:read', 'exercice:write', 'utilisateurs:read'])]
    private ?int $ageMin = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['exercice:read', 'exercice:write', 'utilisateurs:read'])]
    private ?int $ageMax = null;

    #[ORM\Column]
    #[Groups(['exercice:read', 'utilisateurs:read'])]
    private ?\DateTimeImmutable $dateCreation = null;

    #[ORM\OneToMany(targetEntity: Utilisateurs::class, mappedBy: 'exercice')]
    #[Groups(['exercice:read'])]
    private Collection $utilisateurs;

    #[ORM\OneToMany(targetEntity: Commentaire::class, mappedBy: 'exercice', orphanRemoval: true)]
    #[Groups(['exercice:read'])]
    private Collection $commentaires;

    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
        $this->utilisateurs = new ArrayCollection();
        $this->dateCreation = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getInspirationSeconde(): ?int
    {
        return $this->inspirationSeconde;
    }

    public function setInspirationSeconde(?int $inspirationSeconde): static
    {
        $this->inspirationSeconde = $inspirationSeconde;

        return $this;
    }

    public function getRespirationSeconde(): ?int
    {
        return $this->respirationSeconde;
    }

    public function setRespirationSeconde(?int $respirationSeconde): static
    {
        $this->respirationSeconde = $respirationSeconde;

        return $this;
    }

    public function getAgeMin(): ?int
    {
        return $this->ageMin;
    }

    public function setAgeMin(?int $ageMin): static
    {
        $this->ageMin = $ageMin;

        return $this;
    }

    public function getAgeMax(): ?int
    {
        return $this->ageMax;
    }

    public function setAgeMax(?int $ageMax): static
    {
        $this->ageMax = $ageMax;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeImmutable
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeImmutable $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * @return Collection<int, Utilisateurs>
     */
    public function getUtilisateurs(): Collection
    {
        return $this->utilisateurs;
    }

    public function addUtilisateur(Utilisateurs $utilisateur): static
    {
        if (!$this->utilisateurs->contains($utilisateur)) {
            $this->utilisateurs->add($utilisateur);
            $utilisateur->setExercice($this);
        }

        return $this;
    }

    public function removeUtilisateur(Utilisateurs $utilisateur): static
    {
        if ($this->utilisateurs->removeElement($utilisateur)) {
            if ($utilisateur->getExercice() === $this) {
                $utilisateur->setExercice(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): static
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setExercice($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): static
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getExercice() === $this) {
                $commentaire->setExercice(null);
            }
        }

        return $this;
    }

    #[Groups(['exercice:read'])]
    public function getNombreLancements(): int
    {
        return $this->commentaires->count();
    }
}

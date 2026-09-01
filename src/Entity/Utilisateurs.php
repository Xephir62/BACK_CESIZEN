<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\UtilisateursRepository;
use App\State\UserPasswordProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UtilisateursRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Cet email est déjà utilisé.')]
#[UniqueEntity(fields: ['pseudo'], message: 'Ce pseudo est déjà utilisé.')]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_ADMIN') or object == user"),
        new GetCollection(security: "is_granted('ROLE_ADMIN')"),
        new Post(processor: UserPasswordProcessor::class),
        new Put(processor: UserPasswordProcessor::class, security: "is_granted('ROLE_ADMIN') or object == user"),
        new Delete(security: "is_granted('ROLE_ADMIN') or object == user"),
    ],
    normalizationContext: ['groups' => ['utilisateurs:read']],
    denormalizationContext: ['groups' => ['utilisateurs:write']]
)]
class Utilisateurs implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['utilisateurs:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['utilisateurs:read', 'utilisateurs:write'])]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Groups(['utilisateurs:read', 'utilisateurs:write'])]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(max: 255)]
    #[Groups(['utilisateurs:read', 'utilisateurs:write'])]
    private ?string $telephone = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Length(max: 255)]
    #[Groups(['utilisateurs:read', 'utilisateurs:write'])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $motDePasse = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    #[Groups(['utilisateurs:read', 'utilisateurs:write'])]
    private ?string $pseudo = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['utilisateurs:read', 'utilisateurs:write'])]
    private ?string $photoProfil = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['utilisateurs:read', 'utilisateurs:write'])]
    private ?int $age = null;

    #[ORM\Column]
    #[Groups(['utilisateurs:read', 'utilisateurs:write'])]
    private ?bool $statusCompte = null;

    #[ORM\Column]
    #[Groups(['utilisateurs:read', 'utilisateurs:write'])]
    private ?\DateTimeImmutable $dateCreation = null;

    #[ORM\ManyToOne(inversedBy: 'utilisateurs')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['utilisateurs:read'])]
    private ?RolesUtilisateurs $role = null;

    #[ORM\OneToMany(targetEntity: RefreshToken::class, mappedBy: 'utilisateur')]
    #[Groups(['utilisateurs:read'])]
    private Collection $refreshTokens;

    #[ORM\OneToMany(targetEntity: RenitialisationMdp::class, mappedBy: 'utilisateur')]
    #[Groups(['utilisateurs:read'])]
    private Collection $renitialisationMdps;

    #[ORM\ManyToOne(inversedBy: 'utilisateurs')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['utilisateurs:read', 'utilisateurs:write'])]
    private ?Exercice $exercice = null;

    #[ORM\OneToMany(targetEntity: Commentaire::class, mappedBy: 'utilisateur', orphanRemoval: true)]
    #[Groups(['utilisateurs:read'])]
    private Collection $commentaires;

    #[Groups(['utilisateurs:write'])]
    private ?string $plainPassword = null;

    public function __construct()
    {
        $this->refreshTokens = new ArrayCollection();
        $this->renitialisationMdps = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getMotDePasse(): ?string
    {
        return $this->motDePasse;
    }

    public function setMotDePasse(string $motDePasse): static
    {
        $this->motDePasse = $motDePasse;

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

    public function getPhotoProfil(): ?string
    {
        return $this->photoProfil;
    }

    public function setPhotoProfil(?string $photoProfil): static
    {
        $this->photoProfil = $photoProfil;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(?int $age): static
    {
        $this->age = $age;

        return $this;
    }

    public function isStatusCompte(): ?bool
    {
        return $this->statusCompte;
    }

    public function setStatusCompte(bool $statusCompte): static
    {
        $this->statusCompte = $statusCompte;

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

    public function getRole(): ?RolesUtilisateurs
    {
        return $this->role;
    }

    public function setRole(?RolesUtilisateurs $role): static
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return Collection<int, RefreshToken>
     */
    public function getRefreshTokens(): Collection
    {
        return $this->refreshTokens;
    }

    public function addRefreshToken(RefreshToken $refreshToken): static
    {
        if (!$this->refreshTokens->contains($refreshToken)) {
            $this->refreshTokens->add($refreshToken);
            $refreshToken->setUtilisateur($this);
        }

        return $this;
    }

    public function removeRefreshToken(RefreshToken $refreshToken): static
    {
        if ($this->refreshTokens->removeElement($refreshToken)) {
            // set the owning side to null (unless already changed)
            if ($refreshToken->getUtilisateur() === $this) {
                $refreshToken->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RenitialisationMdp>
     */
    public function getRenitialisationMdps(): Collection
    {
        return $this->renitialisationMdps;
    }

    public function addRenitialisationMdp(RenitialisationMdp $renitialisationMdp): static
    {
        if (!$this->renitialisationMdps->contains($renitialisationMdp)) {
            $this->renitialisationMdps->add($renitialisationMdp);
            $renitialisationMdp->setUtilisateur($this);
        }

        return $this;
    }

    public function removeRenitialisationMdp(RenitialisationMdp $renitialisationMdp): static
    {
        if ($this->renitialisationMdps->removeElement($renitialisationMdp)) {
            // set the owning side to null (unless already changed)
            if ($renitialisationMdp->getUtilisateur() === $this) {
                $renitialisationMdp->setUtilisateur(null);
            }
        }

        return $this;
    }

    public function getExercice(): ?Exercice
    {
        return $this->exercice;
    }

    public function setExercice(?Exercice $exercice): static
    {
        $this->exercice = $exercice;

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
            $commentaire->setUtilisateur($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): static
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getUtilisateur() === $this) {
                $commentaire->setUtilisateur(null);
            }
        }

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = [];
        if ($this->role?->getLibelle()) {
            $roles[] = $this->role->getLibelle();
        }
        $roles[] = 'ROLE_USER';

        return array_values(array_unique($roles));
    }

    public function getPassword(): ?string
    {
        return $this->motDePasse;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): static
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }
}

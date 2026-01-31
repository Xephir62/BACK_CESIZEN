<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\RenitialisationMdpRepository;


#[ORM\Entity(repositoryClass: RenitialisationMdpRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Put(),
        new Delete(),
    ],
    normalizationContext: ['groups' => ['renitialisation_mdp:read']],
    denormalizationContext: ['groups' => ['renitialisation_mdp:write']]
)]
class RenitialisationMdp
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['renitialisation_mdp:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $tokenReset = null;

    #[ORM\Column]
    #[Groups(['renitialisation_mdp:read', 'renitialisation_mdp:write'])]
    private ?\DateTimeImmutable $dateDemande = null;

    #[ORM\Column]
    #[Groups(['renitialisation_mdp:read', 'renitialisation_mdp:write'])]
    private ?\DateTimeImmutable $dateExpiration = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['renitialisation_mdp:read', 'renitialisation_mdp:write'])]
    private ?\DateTimeImmutable $dateUtilisation = null;

    #[ORM\ManyToOne(inversedBy: 'renitialisationMdps')]
    #[Groups(['renitialisation_mdp:read', 'renitialisation_mdp:write'])]
    private ?Utilisateurs $utilisateur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTokenReset(): ?string
    {
        return $this->tokenReset;
    }

    public function setTokenReset(string $tokenReset): static
    {
        $this->tokenReset = $tokenReset;

        return $this;
    }

    public function getDateDemande(): ?\DateTimeImmutable
    {
        return $this->dateDemande;
    }

    public function setDateDemande(\DateTimeImmutable $dateDemande): static
    {
        $this->dateDemande = $dateDemande;

        return $this;
    }

    public function getDateExpiration(): ?\DateTimeImmutable
    {
        return $this->dateExpiration;
    }

    public function setDateExpiration(\DateTimeImmutable $dateExpiration): static
    {
        $this->dateExpiration = $dateExpiration;

        return $this;
    }

    public function getDateUtilisation(): ?\DateTimeImmutable
    {
        return $this->dateUtilisation;
    }

    public function setDateUtilisation(?\DateTimeImmutable $dateUtilisation): static
    {
        $this->dateUtilisation = $dateUtilisation;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateurs
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateurs $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }
}

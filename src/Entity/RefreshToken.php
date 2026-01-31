<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\RefreshTokenRepository;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: RefreshTokenRepository::class)]
class RefreshToken implements RefreshTokenInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $refreshToken = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $valid = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\ManyToOne(targetEntity: Utilisateurs::class, inversedBy: 'refreshTokens')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Utilisateurs $utilisateur = null;

    public static function createForUserWithTtl(string $refreshToken, UserInterface $user, int $ttl): RefreshTokenInterface
    {
        $valid = new \DateTime();
        $valid->modify('+' . $ttl . ' seconds');

        $entity = new self();
        $entity->setRefreshToken($refreshToken);
        $entity->setUsername($user->getUserIdentifier());
        $entity->setValid(\DateTimeImmutable::createFromMutable($valid));

        return $entity;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken($refreshToken = null): RefreshTokenInterface
    {
        $this->refreshToken = $refreshToken;
        return $this;
    }

    public function getValid(): ?\DateTimeImmutable
    {
        return $this->valid;
    }

    public function setValid($valid = null): RefreshTokenInterface
    {
        $this->valid = $valid instanceof \DateTime ? \DateTimeImmutable::createFromMutable($valid) : $valid;
        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername($username = null): RefreshTokenInterface
    {
        $this->username = $username;
        return $this;
    }

    public function getUtilisateur(): ?Utilisateurs
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateurs $utilisateur): self
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    public function isValid(): bool
    {
        $datetime = new \DateTimeImmutable();
        return $this->valid >= $datetime;
    }

    public function __toString(): string
    {
        return $this->getRefreshToken() ?: '';
    }
}

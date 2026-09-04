<?php

namespace App\Controller;

use App\Entity\RenitialisationMdp;
use App\Entity\Utilisateurs;
use App\Repository\UtilisateursRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class PasswordController extends AbstractController
{
    #[Route('/api/password/forgot', name: 'api_password_forgot', methods: ['POST'])]
    public function forgot(
        Request $request,
        UtilisateursRepository $utilisateursRepository,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
    ): JsonResponse {
        $payload = json_decode((string) $request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $email = strtolower(trim((string) ($payload['email'] ?? '')));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new JsonResponse(['message' => 'Adresse e-mail invalide.'], Response::HTTP_BAD_REQUEST);
        }

        $user = $utilisateursRepository->findOneBy(['email' => $email]);
        if (null !== $user) {
            $token = $this->createResetToken($user, $entityManager, $mailer);
            $this->sendResetEmail($user, $token, $mailer);
        }

        return new JsonResponse([
            'message' => 'Si un compte existe pour cette adresse, un message de réinitialisation a été envoyé.',
        ]);
    }

    #[Route('/api/password/reset', name: 'api_password_reset', methods: ['POST'])]
    public function reset(
        Request $request,
        UtilisateursRepository $utilisateursRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
    ): JsonResponse {
        $payload = json_decode((string) $request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $email = strtolower(trim((string) ($payload['email'] ?? '')));
        $token = trim((string) ($payload['token'] ?? ''));
        $newPassword = (string) ($payload['password'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || '' === $token || strlen($newPassword) < 8) {
            return new JsonResponse(['message' => 'Les données de réinitialisation sont invalides.'], Response::HTTP_BAD_REQUEST);
        }

        $user = $utilisateursRepository->findOneBy(['email' => $email]);
        if (null === $user) {
            return new JsonResponse(['message' => 'Le lien de réinitialisation est invalide ou expiré.'], Response::HTTP_BAD_REQUEST);
        }

        $tokenHash = hash('sha256', $token);
        $resetRequest = $entityManager->getRepository(RenitialisationMdp::class)
            ->createQueryBuilder('r')
            ->where('r.utilisateur = :user')
            ->andWhere('r.dateUtilisation IS NULL')
            ->andWhere('r.dateExpiration > :now')
            ->andWhere('r.tokenReset = :tokenHash')
            ->setParameter('user', $user)
            ->setParameter('now', new \DateTimeImmutable())
            ->setParameter('tokenHash', $tokenHash)
            ->orderBy('r.dateDemande', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (null === $resetRequest) {
            return new JsonResponse(['message' => 'Le lien de réinitialisation est invalide ou expiré.'], Response::HTTP_BAD_REQUEST);
        }

        $resetRequest->setDateUtilisation(new \DateTimeImmutable());
        $user->setMotDePasse($passwordHasher->hashPassword($user, $newPassword));
        $entityManager->flush();

        return new JsonResponse(['message' => 'Votre mot de passe a bien été réinitialisé.']);
    }

    private function createResetToken(Utilisateurs $user, EntityManagerInterface $entityManager, MailerInterface $mailer): string
    {
        $token = bin2hex(random_bytes(32));

        $existingRequests = $entityManager->getRepository(RenitialisationMdp::class)->findBy([
            'utilisateur' => $user,
            'dateUtilisation' => null,
        ]);

        foreach ($existingRequests as $request) {
            if (null === $request->getDateExpiration() || $request->getDateExpiration() > new \DateTimeImmutable()) {
                $request->setDateUtilisation(new \DateTimeImmutable());
            }
        }

        $resetRequest = new RenitialisationMdp();
        $resetRequest->setUtilisateur($user);
        $resetRequest->setTokenReset(hash('sha256', $token));
        $resetRequest->setDateDemande(new \DateTimeImmutable());
        $resetRequest->setDateExpiration((new \DateTimeImmutable())->modify('+30 minutes'));
        $resetRequest->setDateUtilisation(null);

        $entityManager->persist($resetRequest);
        $entityManager->flush();

        return $token;
    }

    private function sendResetEmail(Utilisateurs $user, string $token, MailerInterface $mailer): void
    {
        $email = (new Email())
            ->from('no-reply@cesizen.local')
            ->to($user->getEmail())
            ->subject('Réinitialisation de votre mot de passe')
            ->html(sprintf(
                '<p>Vous avez demandé une réinitialisation de votre mot de passe.</p>'
                    .'<p>Voici votre jeton de sécurité : <strong>%s</strong></p>'
                    .'<p>Utilisez ce jeton dans le flux /api/password/reset.</p>',
                htmlspecialchars($token, ENT_QUOTES, 'UTF-8')
            ));

        $mailer->send($email);
    }
}

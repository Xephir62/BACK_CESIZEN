<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Commentaire;
use App\Entity\Utilisateurs;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CommentaireProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $persistProcessor,
        private Security $security,
    ) {
    }

    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if ($data instanceof Commentaire) {
            $user = $this->security->getUser();

            if (!$user instanceof Utilisateurs) {
                throw new BadRequestHttpException('Utilisateur authentifie introuvable.');
            }

            $exercice = $user->getExercice();
            if (null === $exercice) {
                throw new BadRequestHttpException('Aucun exercice n est affecte a cet utilisateur.');
            }

            $age = $user->getAge();
            $ageMin = $exercice->getAgeMin();
            $ageMax = $exercice->getAgeMax();

            if (null === $age) {
                throw new BadRequestHttpException('L age de l utilisateur est obligatoire.');
            }

            if (null !== $ageMin && $age < $ageMin) {
                throw new BadRequestHttpException('Cet exercice n est pas disponible pour cet age.');
            }

            if (null !== $ageMax && $age > $ageMax) {
                throw new BadRequestHttpException('Cet exercice n est pas disponible pour cet age.');
            }

            $data->setUtilisateur($user);
            $data->setExercice($exercice);

            if ('' === trim((string) $data->getContenu())) {
                throw new BadRequestHttpException('Le commentaire est obligatoire pour lancer lexercice.');
            }
        }

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}

<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Utilisateurs;
use App\Repository\RolesUtilisateursRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserPasswordProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $persistProcessor,
        private UserPasswordHasherInterface $passwordHasher,
        private RolesUtilisateursRepository $rolesRepo
    ) {}

    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if ($data instanceof Utilisateurs) {
            if ($data->getExercice() !== null) {
                if ($data->getAge() === null) {
                    throw new BadRequestHttpException('L age de l utilisateur est obligatoire pour affecter un exercice.');
                }

                $age = $data->getAge();
                $ageMin = $data->getExercice()->getAgeMin();
                $ageMax = $data->getExercice()->getAgeMax();

                if ($ageMin !== null && $age < $ageMin) {
                    throw new BadRequestHttpException('Cet exercice n est pas disponible pour cet age.');
                }

                if ($ageMax !== null && $age > $ageMax) {
                    throw new BadRequestHttpException('Cet exercice n est pas disponible pour cet age.');
                }
            }

            if ($data->getPlainPassword()) {
                $hash = $this->passwordHasher->hashPassword($data, $data->getPlainPassword());
                $data->setMotDePasse($hash);
                $data->eraseCredentials();
            }

            if ($data->getDateCreation() === null) {
                $data->setDateCreation(new \DateTimeImmutable());
            }

            if ($data->isStatusCompte() === null) {
                $data->setStatusCompte(true);
            }

            $roleUser = $this->rolesRepo->findOneBy(['libelle' => 'ROLE_USER']);
            if ($roleUser) {
                $data->setRole($roleUser);
            }
        }

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}

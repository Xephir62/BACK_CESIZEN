<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class SecurityController extends AbstractController
{
    #[Route('/api/login_check', name: 'api_login_check', methods: ['POST'])]
    public function login(): JsonResponse
    {
        // Ce contrôleur ne sera jamais exécuté car la requête 
        // est interceptée par le firewall json_login
        return new JsonResponse(['message' => 'This should not be reached']);
    }
}

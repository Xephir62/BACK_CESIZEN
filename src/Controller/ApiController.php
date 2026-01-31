<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;

final class ApiController
{
    #[Route('/api', name: 'api_root', methods: ['GET'])]
    public function root(): RedirectResponse
    {
        return new RedirectResponse('/api/docs');
    }
}

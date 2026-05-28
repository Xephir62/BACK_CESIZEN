<?php

namespace App\Controller\Admin;

use App\Entity\Ressource;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class RessourceAdminController extends AbstractController
{
    #[Route('/admin/ressources', name: 'admin_ressource_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse(['message' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $titre = $request->request->get('titre');
        $description = $request->request->get('description');
        $file = $request->files->get('image');

        if (!$titre) {
            return new JsonResponse(['message' => 'Le champ "titre" est requis.'], Response::HTTP_BAD_REQUEST);
        }

        $ressource = new Ressource();
        $ressource->setTitre($titre);
        $ressource->setDescription($description);

        if ($file) {
            $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/ressources';
            if (!is_dir($uploadsDir)) {
                mkdir($uploadsDir, 0755, true);
            }

            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

            try {
                $file->move($uploadsDir, $newFilename);
                $ressource->setImage('/uploads/ressources/' . $newFilename);
            } catch (\Exception $e) {
                return new JsonResponse(['message' => 'Erreur lors de l\'upload de l\'image.'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        $em->persist($ressource);
        $em->flush();

        return $this->json(['id' => $ressource->getId(), 'message' => 'Ressource créée'], Response::HTTP_CREATED);
    }
}

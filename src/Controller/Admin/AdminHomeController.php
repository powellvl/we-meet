<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use App\Repository\LanguageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class AdminHomeController extends AbstractController
{
    #[Route('/admin', name: 'app_admin_home')]
    public function index(
        UserRepository $userRepository,
        LanguageRepository $languageRepository
    ): Response {
        // Statistiques pour le tableau de bord
        $stats = [
            'totalUsers' => $userRepository->count([]),
            'totalLanguages' => $languageRepository->count([]),
        ];

        return $this->render('admin/dashboard.html.twig', [
            'stats' => $stats,
        ]);
    }
}

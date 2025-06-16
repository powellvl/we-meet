<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Repository\LanguageRepository;

class DashboardController extends AbstractController
{
    #[Route('/admin', name: 'app_admin_dashboard')]
    public function index(UserRepository $userRepository, LanguageRepository $languageRepository): Response
    {
        // Vérifier si l'utilisateur a le rôle admin
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        // Statistiques pour le tableau de bord
        $stats = [
            'totalUsers' => $userRepository->count([]),
            'totalLanguages' => $languageRepository->count([]),
            'newUsers' => 0 // À compléter avec la logique pour compter les nouveaux utilisateurs du mois
        ];
        
        return $this->render('Admin/dashboard.html.twig', [
            'stats' => $stats
        ]);
    }
}

<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    // Route temporairement désactivée pour éviter les boucles de redirection
    // #[Route('/admin-index', name: 'app_admin_index')]
    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('app_admin_home');
    }
}

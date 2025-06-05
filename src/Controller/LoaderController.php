<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoaderController extends AbstractController
{
    #[Route('/loader', name: 'app_loader')]
    public function index(): Response
    {
        // Passe la route d'accueil au template
        return $this->render('loader.html.twig', [
            'redirect_to' => $this->generateUrl('app_home')
        ]);
    }
}
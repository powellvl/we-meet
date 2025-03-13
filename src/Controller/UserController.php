<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;

#[Route('/user')]
final class UserController extends AbstractController
{
    /**
     * Vérifie si l'utilisateur a les permissions suffisantes, sinon redirige
     */
    private function checkPermissionOrRedirect(Request $request): ?RedirectResponse
    {
        // Vérifier si l'utilisateur est connecté et a seulement ROLE_USER
        if ($this->getUser() && in_array('ROLE_USER', $this->getUser()->getRoles()) && 
            !in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            // Rediriger vers la page précédente
            $referer = $request->headers->get('referer');
            $this->addFlash('error', 'Vous n\'avez pas les permissions nécessaires pour accéder à cette page.');
            return $this->redirect($referer ?: '/');
        }
        
        return null;
    }

    #[Route(name: 'app_user_index', methods: ['GET'])]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        // Vérifier les permissions
        $redirectResponse = $this->checkPermissionOrRedirect($request);
        if ($redirectResponse) {
            return $redirectResponse;
        }
        
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Vérifier les permissions
        $redirectResponse = $this->checkPermissionOrRedirect($request);
        if ($redirectResponse) {
            return $redirectResponse;
        }
        
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(Request $request, User $user): Response
    {
        // Vérifier les permissions
        $redirectResponse = $this->checkPermissionOrRedirect($request);
        if ($redirectResponse) {
            return $redirectResponse;
        }
        
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        // Vérifier les permissions
        $redirectResponse = $this->checkPermissionOrRedirect($request);
        if ($redirectResponse) {
            return $redirectResponse;
        }
        
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        // Vérifier les permissions
        $redirectResponse = $this->checkPermissionOrRedirect($request);
        if ($redirectResponse) {
            return $redirectResponse;
        }
        
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}

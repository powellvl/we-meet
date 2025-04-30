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
    private function isUserAdmin(): bool
    {
        // Vérifier si l'utilisateur est a le role ROLE_ADMIN
        return($this->getUser() && in_array('ROLE_ADMIN', $this->getUser()->getRoles()));
    }

    private function redirectIfNotAdmin(): ?RedirectResponse
    {
        if (!$this->isUserAdmin()) {
            return $this->redirectToRoute('app_public', [], Response::HTTP_SEE_OTHER);
        }
        return null;
    }

    private function isCurrentUserAllowed($id): bool
    {
        return ((in_array('ROLE_ADMIN', $this->getUser()->getRoles()) || $this->getUser()->getId() == $id));
    }

    #[Route(name: 'app_user_index', methods: ['GET'])]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        // Vérifier les permissions
        $this->redirectIfNotAdmin();
        // if ($redirectResponse) {
        //     return $redirectResponse;
        // }
        
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
        
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Vérifier les permissions
        $this->redirectIfNotAdmin();
        
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
    public function show(Request $request, User $user, $id): Response
    
    {
        // Vérifier les permissions
        if (!$this->isCurrentUserAllowed($id)) {
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }
        
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        // Vérifier les permissions
        $this->redirectIfNotAdmin();
        
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //voir pour une verif des roles
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
        $redirectResponse = $this->isUserAdmin();
        if ($redirectResponse) {
        }
        
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}

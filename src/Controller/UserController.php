<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user')]
#[IsGranted('ROLE_USER')]
class UserController extends AbstractController
{
    private function isCurrentUserAllowed($id): bool
    {
        return ((in_array('ROLE_ADMIN', $this->getUser()->getRoles()) || $this->getUser()->getId() == $id));
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user, int $id): Response
    {
        // Vérifier les permissions
        if (!$this->isCurrentUserAllowed($id)) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à voir ce profil.');
            return $this->redirectToRoute('app_home');
        }
        
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, int $id): Response
    {
        // Vérifier si l'utilisateur peut modifier ce profil
        if (!$this->isCurrentUserAllowed($id)) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à modifier ce profil.');
            return $this->redirectToRoute('app_home');
        }
        
        // Nous devons créer ce formulaire
        $form = $this->createForm(UserProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            $this->addFlash('success', 'Votre profil a été mis à jour avec succès.');
            return $this->redirectToRoute('app_user_show', ['id' => $user->getId()]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}

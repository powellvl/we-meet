<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/professor')]
class AdminProfessorValidationController extends AbstractController
{
    #[Route('/pending', name: 'admin_professor_pending')]
    public function listPending(EntityManagerInterface $em): Response
    {
        $users = $em->getRepository(User::class)->findAll(); // Option: ajouter un filtre plus tard

        return $this->render('admin/pending.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/accept/{id}', name: 'admin_professor_accept')]
    public function accept(User $user, EntityManagerInterface $em): Response
    {
        $roles = $user->getRoles();
        if (!in_array('ROLE_PROFESSOR', $roles)) {
            $roles[] = 'ROLE_PROFESSOR';
            $user->setRoles($roles);
            $em->flush();

            $this->addFlash('success', $user->getFirstname().' est maintenant professeur !');
        }

        return $this->redirectToRoute('admin_professor_pending');
    }

    #[Route('/reject/{id}', name: 'admin_professor_reject')]
    public function reject(User $user): Response
    {
        $this->addFlash('warning', $user->getFirstname().' n\'a pas été accepté.');

        return $this->redirectToRoute('admin_professor_pending');
    }
}
<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/professor')]
#[IsGranted('ROLE_ADMIN')]
class AdminProfessorValidationController extends AbstractController
{
    #[Route('/pending', name: 'admin_professor_pending')]
    public function listPending(EntityManagerInterface $em): Response
    {
        // Récupérer uniquement les utilisateurs avec le rôle ROLE_PENDING_PROFESSOR
        $users = $em->getRepository(User::class)->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%"ROLE_PENDING_PROFESSOR"%')
            ->getQuery()
            ->getResult();

        return $this->render('admin/pending.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/accept/{id}', name: 'admin_professor_accept')]
    public function accept(User $user, EntityManagerInterface $em): Response
    {
        $roles = $user->getRoles();
        
        // Suppression des réponses de l'utilisateur
        $answers = $em->getRepository('App\Entity\Answer')->findBy(['user' => $user]);
        foreach ($answers as $answer) {
            $em->remove($answer);
        }
        $em->flush();
        
        // Supprime le rôle ROLE_PENDING_PROFESSOR
        $newRoles = array_filter($roles, function($role) {
            return $role !== 'ROLE_PENDING_PROFESSOR';
        });
        
        // Ajoute le rôle ROLE_PROFESSOR s'il n'est pas déjà présent
        if (!in_array('ROLE_PROFESSOR', $newRoles)) {
            $newRoles[] = 'ROLE_PROFESSOR';
        }
        
        $user->setRoles($newRoles);
        $em->flush();

        $this->addFlash('success', $user->getFirstname().' est maintenant professeur !');

        return $this->redirectToRoute('admin_professor_pending');
    }

    #[Route('/reject/{id}', name: 'admin_professor_reject')]
    public function reject(User $user, EntityManagerInterface $em): Response
    {
        // Suppression des réponses de l'utilisateur d'abord
        $answers = $em->getRepository('App\Entity\Answer')->findBy(['user' => $user]);
        foreach ($answers as $answer) {
            $em->remove($answer);
        }
        $em->flush();
        
        // Supprime le rôle ROLE_PENDING_PROFESSOR
        $roles = $user->getRoles();
        $newRoles = array_filter($roles, function($role) {
            return $role !== 'ROLE_PENDING_PROFESSOR';
        });
        
        $user->setRoles($newRoles);
        $em->flush();
        
        $this->addFlash('warning', $user->getFirstname().' n\'a pas été accepté.');

        return $this->redirectToRoute('admin_professor_pending');
    }

    #[Route('/answers/{id}', name: 'admin_professor_answers')]
public function viewAnswers(User $user, EntityManagerInterface $em): Response
{
    // Récupérer les réponses au questionnaire pour cet utilisateur
    // Exemple (à adapter selon votre modèle de données) :
    $answers = $em->getRepository('App\Entity\Answer')
        ->findBy(['user' => $user]);
    
    
    
    return $this->render('admin/professor_answers.html.twig', [
        'user' => $user,
        'answers' => $answers ?? [],
    ]);
    }
}

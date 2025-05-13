<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/professor')]
#[IsGranted('ROLE_ADMIN')]
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
            // suppression de l'utilisateur de la liste des utilisateurs en attente
            $em->remove($user);
            $em->flush();

        }

        return $this->redirectToRoute('admin_professor_pending');
    }

    #[Route('/reject/{id}', name: 'admin_professor_reject')]
    public function reject(User $user): Response
    {
        $this->addFlash('warning', $user->getFirstname().' n\'a pas été accepté.');
        // Suppression de l'utilisateur de la liste des utilisateurs en attente
        // Redirection vers la liste des utilisateurs en attente
        // Option: ajouter une notification ou un message à l'utilisateur

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

<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur gérant le processus d'inscription des utilisateurs.
 */
class RegistrationController extends AbstractController
{
    /**
     * Gère la soumission du formulaire d'inscription et la création d'utilisateur.
     *
     * @param Request $request La requête HTTP courante
     * @param UserPasswordHasherInterface $userPasswordHasher Service pour hasher les mots de passe utilisateur
     * @param EntityManagerInterface $entityManager Gestionnaire d'entités Doctrine
     * @return Response La réponse HTTP
     */
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        // Créer une nouvelle entité User
        $user = new User();
        // Créer un formulaire d'inscription pour l'utilisateur
        $form = $this->createForm(RegistrationFormType::class, $user);
        // Traiter la requête entrante
        $form->handleRequest($request);

        // Vérifier si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            // Récupérer le mot de passe en clair depuis le formulaire
            $plainPassword = $form->get('plainPassword')->getData();

            // Hasher le mot de passe de l'utilisateur pour la sécurité
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            // Sauvegarder l'utilisateur dans la base de données
            $entityManager->persist($user);
            $entityManager->flush();
            
            // Rediriger vers la page de sélection de langue avec l'ID du nouvel utilisateur
            return $this->redirectToRoute('app_language_selection', ['id' => $user->getId()]);
        }

        // Afficher le template du formulaire d'inscription si le formulaire n'est pas soumis ou pas valide
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}

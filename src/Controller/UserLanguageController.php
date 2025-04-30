<?php

namespace App\Controller;

use App\Entity\LanguageManagement;
use App\Entity\User;
use App\Form\LanguageManagementCollectionType;
use App\Repository\LanguageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/register')]
class UserLanguageController extends AbstractController
{
    #[Route('/languages/{id}', name: 'app_language_selection')]
    public function selectLanguages(
        User $user,
        Request $request,
        EntityManagerInterface $em,
        LanguageRepository $languageRepository
    ): Response {
        // Pré-remplir les langues si vide
        if ($user->getLanguageManagements()->isEmpty()) {
            $languages = $languageRepository->findAll();
            foreach ($languages as $language) {
                $lm = new LanguageManagement();
                $lm->setUser($user);
                $lm->setLanguage($language);
                $user->addLanguageManagement($lm);
            }
        }

        $form = $this->createForm(LanguageManagementCollectionType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hasAtLeastOneSelection = false;
            $wantsToBeProfessor = false;

            foreach ($user->getLanguageManagements() as $lm) {
                if ($lm->getLanguage() !== null) {
                    $hasAtLeastOneSelection = true;
                    $em->persist($lm);

                    if ($lm->isProfessor()) {
                        $wantsToBeProfessor = true;
                    }
                }
            }

            if ($hasAtLeastOneSelection) {
                $em->flush();
                $this->addFlash('success', 'Langue(s) enregistrée(s) avec succès !');

                if ($wantsToBeProfessor) {
                    // ➡️ Redirection vers le questionnaire
                    return $this->redirectToRoute('app_register_questionnaire', ['id' => $user->getId()]);
                } else {
                    // ➡️ Sinon vers accueil ou dashboard
                    return $this->redirectToRoute('app_home'); // adapte le nom de ta route ici
                }
            } else {
                $this->addFlash('warning', 'Veuillez choisir au moins une langue.');
            }
        }

        return $this->render('registration/languages.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
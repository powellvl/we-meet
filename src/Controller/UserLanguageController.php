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
  #[Route('/register/languages/{id}', name: 'app_language_selection')]
  public function selectLanguages(
      User $user,
      Request $request,
      EntityManagerInterface $em,
      LanguageRepository $languageRepository
  ): Response {
      // S’il n’a encore rien rempli, on initialise toutes les langues (optionnel)
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
  
          foreach ($user->getLanguageManagements() as $lm) {
              if ($lm->getLanguage() !== null) {
                  $hasAtLeastOneSelection = true;
                  $em->persist($lm);
              }
          }
  
          if ($hasAtLeastOneSelection) {
              $em->flush();
              $this->addFlash('success', 'Langue(s) enregistrée(s) avec succès !');
              return $this->redirectToRoute('app_register_questionnaire', ['id' => $user->getId()]);
          } else {
              $this->addFlash('warning', 'Veuillez choisir au moins une langue.');
          }
      }
  
      return $this->render('register/languages.html.twig', [
          'form' => $form->createView(),
      ]);
  }
}
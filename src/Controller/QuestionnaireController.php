<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\User;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/register')]
class QuestionnaireController extends AbstractController
{
    #[Route('/questionnaire/{id}', name: 'app_register_questionnaire')]
    public function questionnaire(
        User $user,
        Request $request,
        QuestionRepository $questionRepository,
        EntityManagerInterface $em
    ): Response {
        // 1. Récupérer toutes les langues où l'utilisateur veut être professeur
        $profLanguages = $user->getLanguageManagements()
            ->filter(fn($lm) => $lm->isProfessor())
            ->map(fn($lm) => $lm->getLanguage());

        // 2. Récupérer toutes les questions générales + spécifiques aux langues
        $questions = $questionRepository->findQuestionsForLanguages($profLanguages->toArray());

        if (empty($questions)) {
            $this->addFlash('warning', 'Aucune question n\'a été trouvée pour votre profil.');
            return $this->redirectToRoute('app_home'); // ou autre
        }

        // 3. Créer dynamiquement le formulaire
        $formBuilder = $this->createFormBuilder();

        foreach ($questions as $question) {
            $formBuilder->add('q_' . $question->getId(), TextareaType::class, [
                'label' => $question->getLabel(),
                'required' => true,
            ]);
        }

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        // 4. Si le formulaire est soumis
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($questions as $question) {
                $fieldName = 'q_' . $question->getId();

                $answer = new Answer();
                $answer->setUser($user);
                $answer->setQuestion($question);
                $answer->setContent($form->get($fieldName)->getData());
                $answer->setCreatedAt(new \DateTimeImmutable());
                $answer->setScore(0); // Le score sera calculé plus tard

                $em->persist($answer);
            }

            $em->flush();

            $this->addFlash('success', 'Merci pour vos réponses !');

            return $this->redirectToRoute('app_home'); // ou page "en attente de validation"
        }

        // 5. Afficher le formulaire
        return $this->render('registration/questionnaire.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
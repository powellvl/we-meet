<?php

namespace App\Controller;

use App\Entity\Room;
use App\Form\RoomType;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(RoomRepository $roomRepository, Request $request, EntityManagerInterface $em): Response
    {
        // Récupérer les filtres depuis l'URL
        $title = $request->query->get('title', '');
        $language = $request->query->get('language', '');
        $date = $request->query->get('date', '');
        
        // Récupérer tous les événements
        $rooms = $roomRepository->findAll();

        // Prépare les données pour JS
        $roomData = array_map(function (Room $room) {
            return [
                'id' => $room->getId(),
                'title' => $room->getTitle(),
                'description' => $room->getDescription(),
                'date' => $room->getDate()->format('Y-m-d H:i:s'),
                'latitude' => $room->getLatitude(),
                'longitude' => $room->getLongitude(),
                'creatorId' => $room->getCreator() ? $room->getCreator()->getId() : null,
                'language' => $room->getLanguage() ? [
                    'id' => $room->getLanguage()->getId(),
                    'name' => $room->getLanguage()->getName(),
                ] : null,
                'creator' => $room->getCreator() ? [
                    'id' => $room->getCreator()->getId(),
                    'firstname' => $room->getCreator()->getFirstname(),
                    'lastname' => $room->getCreator()->getLastname(),
                ] : null,
                'participants' => array_map(function ($participant) {
                    return [
                        'id' => $participant->getId(),
                        'firstname' => $participant->getFirstname(),
                        'lastname' => $participant->getLastname(),
                    ];
                }, $room->getParticipants()->toArray()),
            ];
        }, $rooms);

        // Création de room via formulaire
        $room = new Room();
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $room->setCreator($this->getUser());
            $em->persist($room);
            $em->flush();

            $this->addFlash('success', 'Événement créé avec succès !');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/index.html.twig', [
            'rooms' => $roomData,
            'form' => $form->createView(),
            'currentUserId' => $this->getUser()?->getId(),
            'filter_title' => $title,
            'filter_language' => $language,
            'filter_date' => $date,
        ]);
    }

#[Route('/room/{id}/join', name: 'app_room_join')]
public function joinRoom(Room $room, EntityManagerInterface $em): Response
{
    // Récupérer l'utilisateur actuel
    $user = $this->getUser();

    // Vérifier si l'utilisateur est connecté
    if (!$user) {
        $this->addFlash('error', 'Vous devez être connecté pour rejoindre une activité.');
        return $this->redirectToRoute('app_login');
    }

    // Vérifier si l'utilisateur est le créateur
    if ($room->getCreator() && $room->getCreator()->getId() === $user->getId()) {
        $this->addFlash('warning', 'Vous êtes déjà l\'organisateur de cette activité.');
        return $this->redirectToRoute('app_home');
    }

    // Vérifier si l'utilisateur participe déjà
    if ($room->getParticipants()->contains($user)) {
        $this->addFlash('info', 'Vous participez déjà à cette activité.');
        return $this->redirectToRoute('app_home');
    }

    // Vérifier s'il reste des places (maximum 4 participants)
    if ($room->getParticipants()->count() >= 4) {
        $this->addFlash('error', 'Cette activité est complète.');
        return $this->redirectToRoute('app_home');
    }

    // Ajouter l'utilisateur aux participants
    $room->addParticipant($user);
    
    // Persister les changements
    $em->flush();

    $this->addFlash('success', 'Vous avez rejoint l\'activité avec succès !');
    return $this->redirectToRoute('app_home');
}
}
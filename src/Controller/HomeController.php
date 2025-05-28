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
        ]);
    }
}
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
use App\Entity\User;

#[Route('/room')]
class RoomController extends AbstractController
{
    #[Route('/', name: 'app_room_index')]
    public function index(RoomRepository $roomRepository, Request $request, EntityManagerInterface $em): Response
    {
        $rooms = $roomRepository->findAll();

        $room = new Room();
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($room);
            $em->flush();

            $this->addFlash('success', 'Événement créé avec succès !');

            return $this->redirectToRoute('app_room_index');
        }

        return $this->render('room/index.html.twig', [
            'rooms' => $rooms,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/join', name: 'room_join')]
    public function joinRoom(Room $room, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour rejoindre une room.');
        }

        if (!$room->getParticipants()->contains($user)) {
            $room->addParticipant($user);
            $em->persist($room);
            $em->flush();

            $this->addFlash('success', 'Vous avez rejoint l\'événement avec succès.');
        } else {
            $this->addFlash('warning', 'Vous faites déjà partie de cet événement.');
        }

        return $this->redirectToRoute('app_home');
    }
}
<?php

namespace App\Tests\Unit\Entity;

use tests\Fixtures\UserFixturesProfessor;
use App\Entity\User;
use App\Entity\Language;
use App\Tests\Fixtures\LanguageFixtures;
use App\Entity\Room;
use PHPUnit\Framework\TestCase;

/**
 * Ce fichier contient des tests unitaires pour l'entité Task.
 * Les tests permettent de vérifier que l'objet Task se comporte comme attendu
 * sans dépendre d'une base de données ou d'autres services.
 */
class RoomTest extends TestCase
{
    /**
     * Vérifie qu'on peut créer une tâche avec des données valides
     * et que les propriétés sont bien définies.
     * On teste aussi ici le lien entre une tâche et une liste de tâches.
     */
    public function testRoomCreationWithValidData()
    {
      // Création d'une room
      $room = new Room();
      
      // Instantiation d'un user Professor
      $user = UserFixturesProfessor();
      // Instantiation d'une langue
      $language = LanguageFixtures();
      
      // On remplit les infos de la room
      $language = new Language();
      $room->setTitle("Test Room");
      $room->setDescription("Test description");
      $room->setDate(new \DateTime('2025-10-01 12:00:00'));
      $room->setLanguage($language);
      $room->setLatitude(48.8566);
      $room->setLongitude(2.3522);
      $room->setCreator($user);

      // Vérification des valeurs
      $this->assertEquals('Test Room', $room->getTitle());
      $this->assertEquals('Test description', $room->getDescription());
      $this->assertEquals('2025-10-01 12', $room->getDate()->format('Y-m-d H:i:s'));
      $this->assertEquals(48.8566, $room->getLatitude());
      $this->assertEquals(2.3522, $room->getLongitude());
      $this->assertSame($language, $room->getLanguage());
      $this->assertSame($user, $room->getCreator());
    }

}
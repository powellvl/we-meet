<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // User de test
        $user = new User();
        $user->setFirstname('John');
        $user->setLastname('Doe');
        $user->setEmail('test@example.com');
        $user->setRoles(['ROLE_USER']);

        $hashedPassword = $this->passwordHasher->hashPassword($user, 'password123');
        $user->setPassword($hashedPassword);

        $manager->persist($user);
        $manager->flush();
    }
}

class UserFixturesProfessor extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // User de test
        $user = new User();
        $user->setFirstname('Jane');
        $user->setLastname('Doe');
        $user->setEmail('test@example.com');
        $user->setRoles(['ROLE_USER', 'ROLE_PROFESSOR']);

        $hashedPassword = $this->passwordHasher->hashPassword($user, 'password123');
        $user->setPassword($hashedPassword);

        $manager->persist($user);
        $manager->flush();
    }
}

?>
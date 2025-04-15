<?php

use App\Entity\Language;
use App\Entity\Question;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class QuestionFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $language = new Language();
        $language->setName('English');
        $manager->persist($language);

        for ($i = 1; $i <= 5; $i++) {
            $q = new Question();
            $q->setLabel("Question $i");
            $q->setType('text');
            $q->setCategory('grammaire');
            $q->setWeight(10);
            $q->setLanguage($language); // ou null pour question générale
            $manager->persist($q);
        }

        $language = new Language();
        $language->setName('French');
        $manager->persist($language);
        for ($i = 1; $i <= 5; $i++) {
            $q = new Question();
            $q->setLabel("Question $i");
            $q->setType('text');
            $q->setCategory('grammaire');
            $q->setWeight(10);
            $q->setLanguage($language); // ou null pour question générale
            $manager->persist($q);
        }
        $language = new Language();
        $language->setName('Spanish');
        $manager->persist($language);
        for ($i = 1; $i <= 5; $i++) {
            $q = new Question();
            $q->setLabel("Question $i");
            $q->setType('text');
            $q->setCategory('grammaire');
            $q->setWeight(10);
            $q->setLanguage($language); // ou null pour question générale
            $manager->persist($q);
        }
        $language = new Language();
        $language->setName('German');
        $manager->persist($language);
        for ($i = 1; $i <= 5; $i++) {
            $q = new Question();
            $q->setLabel("Question $i");
            $q->setType('text');
            $q->setCategory('grammaire');
            $q->setWeight(10);
            $q->setLanguage($language); // ou null pour question générale
            $manager->persist($q);
        }
        $language = new Language();
        $language->setName('Italian');
        $manager->persist($language);
        for ($i = 1; $i <= 5; $i++) {
            $q = new Question();
            $q->setLabel("Question $i");
            $q->setType('text');
            $q->setCategory('grammaire');
            $q->setWeight(10);
            $q->setLanguage($language); // ou null pour question générale
            $manager->persist($q);
        }

        $manager->flush();
    }
}

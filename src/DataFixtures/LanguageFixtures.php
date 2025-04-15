    <?php

    namespace App\DataFixtures;

    use App\Entity\Language;
    use Doctrine\Bundle\FixturesBundle\Fixture;
    use Doctrine\Persistence\ObjectManager;

    class LanguageFixtures extends Fixture
    {
        public function load(ObjectManager $manager): void
        {
            $language = new Language();
            $language->setName('French');
            $manager->persist($language);

            $language = new Language();
            $language->setName('English');
            $manager->persist($language);

            $language = new Language();
            $language->setName('Spanish');
            $manager->persist($language);

            $language = new Language();
            $language->setName('German');
            $manager->persist($language);

            $language = new Language();
            $language->setName('Italian');
            $manager->persist($language);

            $language = new Language();
            $language->setName('Portuguese');
            $manager->persist($language);

            $language = new Language();
            $language->setName('Russian');
            $manager->persist($language);

            $language = new Language();
            $language->setName('Chinese');
            $manager->persist($language);
            $language = new Language();
            $language->setName('Japanese');
            $manager->persist($language);
            
            $language = new Language();
            $language->setName('Arabic');
            $manager->persist($language);
            
            $manager->flush();
        }
    }

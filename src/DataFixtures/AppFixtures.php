<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Author;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{

    private const NB_ARTICLES = 50;
    private const NB_AUTHORS = 5;
    private const NB_USERS = 10;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
       
        $authors =[];

        // --AUTHORS-----------------------------
        for ($i = 0; $i < self::NB_AUTHORS; $i++) {
            $author = new Author();
            $author
                ->setFirstName($faker->firstName)
                ->setLastName($faker->lastName);

            $manager->persist($author);
            $authors[] = $author;

        }

        // --ARTICLES-----------------------------
        for ($i = 0; $i < self::NB_ARTICLES; $i++) {
            $user = new Article();
            $user
                ->setTitle($faker->realText(85))
                ->setContent($faker->realTextBetween(400, 1500))
                ->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 month', '+1 month')))
                ->setAuthor($faker->randomElement($authors));
        
            $manager->persist($user);
        }
        
        // --USER-----------------------------
        for ($i = 0; $i < self::NB_USERS; $i++) {
            $user = new User();
            $user
                ->setEmail($faker->safeEmail());
        
            $manager->persist($user);
        }



        $manager->flush();
    }
}

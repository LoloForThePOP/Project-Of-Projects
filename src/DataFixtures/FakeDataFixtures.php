<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\PPBase;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class FakeDataFixtures extends Fixture
{

    protected $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }



    public function load(ObjectManager $manager)
    {

        $faker = Factory::create('fr_FR');


        // Project Categories Creation

        // Categories List

        // [position; unique name; english description; french description] 

        $categories = [

            0 =>

            [
                "software",
                "Computers, Code, Internet",
                "Informatique, Codage, Internet",

            ],

            1 =>

            [
                "science",
                "Science, Research",
                "Science, Recherche",

            ],

            2 =>

            [
                "inform",
                "Inform, Educate, Learn",
                "Informer, Éduquer, Apprendre",

            ],

            3 =>

            [
                "humane",
                "Live Together, Humanitarianism",
                "Vivre Ensemble, Humanitaire",

            ],

            4 =>

            [
                "material",
                "Make, Construct, Renovate",
                "Fabriquer, Construire, Rénover",

            ],

            5 =>

            [
                "environment",
                "Environment",
                "Environement",

            ],

            6 =>

            [
                "history",
                "History, Heritage",
                "Histoire, Patrimoine",

            ],

            7 =>

            [
                "animals",
                "Animals",
                "Animaux",

            ],

            8 =>

            [
                "money",
                "Finance, Money",
                "Finance, Argent",

            ],

            9 =>

            [
                "food",
                "Agriculture, Food",
                "Agriculture, Nourriture",

            ],

            10 =>

            [
                "services",
                "Services, Linking",
                "Services, Mise en relation",

            ],

            11 =>

            [
                "arts",
                "Culture, Arts",
                "Culture, Arts",

            ],

            12 =>

            [
                "entertainment",
                "Entertainment, Leasure, Sports",
                "Divertissements, Loisirs, Sports",

            ],

            13 =>

            [
                "data",
                "Organize data",
                "Organiser des données",

            ],

            14 =>

            [
                "health",
                "Health",
                "Santé",

            ],

            15 =>

            [
                "ideas",
                "Ideas, Politics",
                "Idées, Politique",

            ],

            16 =>

            [
                "space",
                "Air and Space",
                "Air et Espace",

            ],

            17 =>

            [
                "crisis",
                "Crisis",
                "Crise",

            ],




        ];

        // store categories as objects, to further hydrate project presentations

        $categoriesObjects = [];

        foreach ($categories as $key => $value) {

            $category = new Category();

            $category->setPosition($key)
                ->setUniqueName($value[0])
                ->setDescriptionEn($value[1])
                ->setDescriptionFr($value[2]);

            $manager->persist($category);

            $categoriesObjects[] = $category;
        }
        // End of Project Categories Creation


        // one admin user creation

        $admin = new User();

        $admin
            ->setUsername(TestUserName)
            ->setEmail('test@test.com')
            ->setPassword(
                $this->encoder->encodePassword(
                    $admin,
                    'test'
                )
            )
            ->setParameter('isVerified', true)
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);


        // Users Creation

        for ($i = 0; $i < 10; $i++) {

            $user = new User();

            $user
                ->setUsername($faker->userName())            
                ->setEmail($faker->email())
                ->setPassword($this->encoder->encodePassword(
                    $admin,
                    'test'
                ))
                ->setParameter('isVerified', true);

            $manager->persist($user);

            // End Of Users Creation



            // Project Presentations Creation

            for ($j = 0; $j < mt_rand(0, 5); $j++) {

                $presentation = new PPBase();

                // Title Creation

                $title = null;

                if ($faker->boolean(50)) {

                    $title = $faker->sentence();
                }

                // Keywords Creation

                $keywordsNumber = mt_rand(0, 7);

                if ($keywordsNumber == 0) {
                    $keywords = '';
                } else {
                    $keywords = join(', ', $faker->words($keywordsNumber));
                }

                // Text Description

                $textDescription = null;

                if ($faker->boolean(75)) {

                    $paragraphsCount = mt_rand(1, 5);

                    $textDescription = '<p>' . join('</p><p>', $faker->paragraphs($paragraphsCount)) . '</p>';
                }

                // Private Messages Activation

                $privateMessagesActivation = false;

                if ($faker->boolean(70)) {

                    $privateMessagesActivation = true;
                }


                // Hydrate Presentation

                $presentation->setCreator($user)
                    ->setGoal($faker->sentence())
                    ->setTitle($title)
                    ->setTextDescription($textDescription)
                    ->setKeywords($keywords)
                    ->setParameter('arePrivateMessagesActivated', $privateMessagesActivation)
                    ->setCreatedAt($faker->dateTimeThisDecade());


                // Project Categories (= Add some categories to this Project Presentation)

                // number of categories for this project
                $numCat = mt_rand(0, 6);

                if ($numCat > 0) {

                    //select some categories at random

                    $catRandKeys = array_rand($categoriesObjects, $numCat);

                    if ($numCat == 1) {

                        $presentation->addCategory($categoriesObjects[$numCat]);
                    } else {

                        foreach ($catRandKeys as $index) {

                            $presentation->addCategory($categoriesObjects[$index]);
                        }
                    }
                }

                $manager->persist($presentation);
            }
        }

        $manager->flush();
    }
}

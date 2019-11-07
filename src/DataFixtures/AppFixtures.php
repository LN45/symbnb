<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Role;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
     $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR');

        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        $adminUser = new User();
        $adminUser->setFirstName('Hélène')
            ->setLastName('Bloch')
            ->setEmail('ln-45@live.fr')
            ->setPassword($this->encoder->encodePassword($adminUser,'password'))
            ->setPicture('https://i.goopics.net/yyL4N.jpg')
            ->setIntroduction($faker->sentence)
            ->setDescription($faker->paragraph(3))
            ->addUserRole($adminRole);
        $manager->persist($adminUser);

        // Nous gérons les utilisateurs
        $users = [];
        $genres = ['male', 'female'];

        for ($i=1; $i<=10; $i++) {
            $user = new User();

            $genre = $faker->randomElement($genres); // on demande à Faker de donner 1 élément au hasard dans le tableau de genre

            // url pour avoir des avatars aléatoirs
            $picture = 'https://randomuser.me/api/portraits/';
            $pictureId = $faker->numberBetween(1,99).'.jpg';

            //condition ternaire = si le genre est égal à 'male' alors on prend '/men' sinon si ce n'est pas le cas on prend 'women'
            $picture .= ($genre == 'male' ? 'men/' : 'women/'). $pictureId;

            $hash = $this->encoder->encodePassword($user, 'password');

            $user->setFirstName($faker->firstName($genre))
                ->setLastName($faker->lastName)
                ->setIntroduction($faker->sentence())
                ->setDescription($faker->paragraph(3))
                ->setEmail($faker->email)
                ->setHash($hash)
                ->setPicture($picture);
            $manager->persist($user);
            $users[] = $user;
        }

        // Nous gérons les annonces
        //creation 30 Ads
        for ($i = 1; $i <= 30; $i++) {
            $ad = new Ad();

            $title = $faker->sentence();
            $coverImage = $faker->imageUrl(1000,350);
            $introduction = $faker->paragraph(2);
            $content =$faker->paragraph(5);

            $user = $users[mt_rand(0, count($users)-1)]; // choisir aléatoirement un user dans le tableau de tous les users

            $ad->setTitle($title)
                ->setCoverImage($coverImage)
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setPrice(mt_rand(40,200))
                ->setRooms(mt_rand(1,5))
                ->setAuthor($user);

            // creation 2, 3, 4 or 5 images for every Ad
            for($j = 1; $j <= mt_rand(2,5); $j++) {
                $image = new Image();
                $image->setUrl($faker->imageUrl())
                    ->setCaption($faker->sentence())
                    ->setAd($ad);
                $manager->persist($image);
            }

            // gestion des réservations
            for($j = 1; $j <= mt_rand(0,10); $j++) {
                $booking = new Booking();

                $createdAd = $faker->dateTimeBetween('-6 months');
                $startDate = $faker->dateTimeBetween('-3 months');
                //gestion de la date de fin
                $duration = mt_rand(3,10);
                $endDate = (clone $startDate)->modify("+$duration days"); // clone le startdate car sinon ça modifirait la date de début et de fin

                $amount = $ad->getPrice() * $duration;
                $booker = $users[mt_rand(0, count($users)-1)];  //prend un utilisateur au hasard dans mon tableau des utilisateurs
                $comment = $faker->paragraph();

                $booking->setBooker($booker)
                    ->setAd($ad)
                    ->setStartDate($startDate)
                    ->setEndDate($endDate)
                    ->setCReatedAt($createdAd)
                    ->setAmount($amount)
                    ->setComment($comment);

                $manager->persist($booking);

                // Gestion des commentaires
                if(mt_rand(0,1)) {
                    $comment = new Comment();
                    $comment->setContent($faker->paragraph())
                        ->setRating(mt_rand(1,5))
                        ->setAuthor($booker)
                        ->setAd($ad);

                    $manager->persist($comment);
                }
            }

            $manager->persist($ad);
        }
        $manager->flush();
    }
}

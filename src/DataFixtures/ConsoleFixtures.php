<?php

namespace App\DataFixtures;

use App\Entity\Console;
use App\Entity\User;
use App\DataFixtures\UserFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ConsoleFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            GenreModeFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        /** @var User $user */
        $user = $this->getReference(UserFixtures::USER_REFERENCE, User::class);

        $list = [
            [
                'name' => 'Nintendo Switch',
                'manufacturer' => 'Nintendo',
                'generation' => 8,
                'releaseDate' => '2017-03-03',
                'image' => 'https://example.com/images/switch.jpg',
                'maxPlayers' => 4,
            ],
            [
                'name' => 'PlayStation 5',
                'manufacturer' => 'Sony',
                'generation' => 9,
                'releaseDate' => '2020-11-12',
                'image' => 'https://example.com/images/ps5.jpg',
                'maxPlayers' => 4,
            ],
            [
                'name' => 'Xbox Series X',
                'manufacturer' => 'Microsoft',
                'generation' => 9,
                'releaseDate' => '2020-11-10',
                'image' => 'https://example.com/images/xbox_series_x.jpg',
                'maxPlayers' => 4,
            ],
        ];

        foreach ($list as $data) {
            $c = new Console();
            $c->setName($data['name']);
            $c->setManufacturer($data['manufacturer']);
            $c->setGeneration($data['generation']);
            $c->setReleaseDate(new \DateTimeImmutable($data['releaseDate']));
            $c->setCreator($user);
            $c->setImage($data['image']);
            $c->setMaxPlayers($data['maxPlayers']);
            // addedAt initialized in entity constructor
            $manager->persist($c);
        }

        $manager->flush();
    }
}

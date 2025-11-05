<?php

namespace App\DataFixtures;

use App\Entity\Genre;
use App\Entity\Mode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GenreModeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $genres = ['Action', 'Aventure', 'RPG', 'Course', 'Plateforme', 'Puzzle', 'Stratégie'];
        foreach ($genres as $name) {
            $g = new Genre();
            $g->setName($name);
            $manager->persist($g);
            $this->addReference('genre_'.$name, $g);
        }

        $modes = ['Solo', 'Multijoueur', 'Coop', 'Compétitif', 'En ligne'];
        foreach ($modes as $name) {
            $m = new Mode();
            $m->setName($name);
            $manager->persist($m);
            $this->addReference('mode_'.$name, $m);
        }

        $manager->flush();
    }
}


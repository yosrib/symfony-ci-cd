<?php

namespace App\DataFixtures;

use App\Entity\Site;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SiteFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        $manager->persist(
            (new Site())
                ->setName('google')
                ->setUrl('https://www.google.com')
        );

        $manager->flush();
    }
}

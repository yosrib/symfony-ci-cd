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
                ->setName('Symfony')
                ->setUrl('https://symfony.com')
        );
        $manager->persist(
            (new Site())
                ->setName('Doctrine')
                ->setUrl('https://www.doctrine-project.org')
        );
        $manager->persist(
            (new Site())
                ->setName('google')
                ->setUrl('https://www.google.com')
        );
        $manager->persist(
            (new Site())
                ->setName('aws')
                ->setUrl('https://aws.amazon.com')
        );
        $manager->persist(
            (new Site())
                ->setName('Docker')
                ->setUrl('https://www.docker.com')
        );
        $manager->persist(
            (new Site())
                ->setName('kubernetes')
                ->setUrl('https://kubernetes.io')
        );
        $manager->persist(
            (new Site())
                ->setName('ovh')
                ->setUrl('https://www.ovh.com')
        );
        $manager->persist(
            (new Site())
                ->setName('React JS')
                ->setUrl('https://fr.reactjs.org')
        );

        $manager->flush();
    }
}

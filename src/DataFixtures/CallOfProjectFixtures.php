<?php

namespace App\DataFixtures;

use App\Entity\CallOfProject;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CallOfProjectFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
         $callOfProject = new CallOfProject();
         $callOfProject->setDescription('Description de de l\'appel à projet');
         $manager->persist($callOfProject);

        $manager->flush();
    }
}

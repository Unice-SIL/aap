<?php

namespace App\DataFixtures;

use App\Entity\CallOfProject;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CallOfProjectFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $callOfProject = new CallOfProject();
        $callOfProject->setDescription('Description de de l\'appel à projet');

        $ref = $this->getReference(ProjectFormLayoutFixtures::class . ProjectFormLayoutFixtures::FORM_LAYOUT_1);
        $callOfProject->setProjectFormLayout($ref);

        $manager->persist($callOfProject);

        $manager->flush();
    }

    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        return [
            ProjectFormLayoutFixtures::class,
        ];
    }
}

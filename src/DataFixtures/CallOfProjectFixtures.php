<?php

namespace App\DataFixtures;

use App\Entity\CallOfProject;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CallOfProjectFixtures extends Fixture implements DependentFixtureInterface
{
    const CALL_OF_PROJECT_1 = 'Appel_projet_1';

    public function load(ObjectManager $manager)
    {
        $callOfProject = new CallOfProject();
        $callOfProject->setName(self::CALL_OF_PROJECT_1);
        $callOfProject->setDescription('Description de l\'appel à projet');

        $this->addReference(self::class . self::CALL_OF_PROJECT_1, $callOfProject);
        $projectFormLayout = $this->getReference(ProjectFormLayoutFixtures::class . ProjectFormLayoutFixtures::FORM_LAYOUT_1);
        $callOfProject->addProjectFormLayout($projectFormLayout);

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

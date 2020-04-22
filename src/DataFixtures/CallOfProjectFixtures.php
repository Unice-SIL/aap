<?php

namespace App\DataFixtures;

use App\Entity\CallOfProject;
use App\Entity\ProjectFormLayout;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CallOfProjectFixtures extends Fixture implements DependentFixtureInterface
{
    const CALL_OF_PROJECT_1 = 'Appel_projet_1';
    const CALL_OF_PROJECT_2 = 'Appel_projet_2';

    public function load(ObjectManager $manager)
    {
        /**
         * CAP 1
         */
        $callOfProject = new CallOfProject();
        $callOfProject->setName(self::CALL_OF_PROJECT_1);
        $callOfProject->setDescription('Description de l\'appel à projet');

        $callOfProject->setStartDate((new \DateTime())->modify('-1 day'));
        $callOfProject->setEndDate((new \DateTime())->modify('+1 day'));

        $this->addReference(self::class . self::CALL_OF_PROJECT_1, $callOfProject);

        /** @var ProjectFormLayout $projectFormLayout */
        $projectFormLayout = $this->getReference(ProjectFormLayoutFixtures::class . ProjectFormLayoutFixtures::FORM_LAYOUT_1);
        $callOfProject->addProjectFormLayout($projectFormLayout);

        /** @var User $user */
        $user = $this->getReference(UserFixtures::class . UserFixtures::USER_ADMIN);
        $callOfProject->setCreatedBy($user);

        $manager->persist($callOfProject);

        /**
         * CAP 2
         */
        $callOfProject = new CallOfProject();
        $callOfProject->setName(self::CALL_OF_PROJECT_2);
        $callOfProject->setDescription('Description de l\'appel à projet');

        $callOfProject->setStartDate((new \DateTime())->modify('-1 day'));
        $callOfProject->setEndDate((new \DateTime())->modify('+1 day'));

        $this->addReference(self::class . self::CALL_OF_PROJECT_2, $callOfProject);

        /** @var ProjectFormLayout $projectFormLayout */
        $projectFormLayout = $this->getReference(ProjectFormLayoutFixtures::class . ProjectFormLayoutFixtures::FORM_LAYOUT_2);
        $callOfProject->addProjectFormLayout($projectFormLayout);

        /** @var User $user */
        $user = $this->getReference(UserFixtures::class . UserFixtures::USER_USER1);
        $callOfProject->setCreatedBy($user);

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
            UserFixtures::class
        ];
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\Project;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProjectFixtures extends Fixture implements DependentFixtureInterface
{

    const PROJECT_1 = 'Projet_1';
    const PROJECT_2 = 'Projet_2';
    const PROJECT_3 = 'Projet_3';
    const PROJECT_4 = 'Projet_4';
    const PROJECT_5 = 'Projet_5';
    const PROJECT_6 = 'Projet_6';
    const PROJECT_7 = 'Projet_7';
    const PROJECT_8 = 'Projet_8';
    const PROJECT_9 = 'Projet_9';
    const PROJECT_10 = 'Projet_10';
    const PROJECT_11 = 'Projet_11';
    const PROJECT_12 = 'Projet_12';

    public function load(ObjectManager $manager)
    {
        $projects = [
            self::PROJECT_1 => [
                'name' => self::PROJECT_1,
                'call_of_project' => CallOfProjectFixtures::CALL_OF_PROJECT_1,
                'createdBy' => UserFixtures::USER_ADMIN,
                'number' => 1
            ],
            self::PROJECT_2 => [
                'name' => self::PROJECT_2,
                'call_of_project' => CallOfProjectFixtures::CALL_OF_PROJECT_1,
                'createdBy' => UserFixtures::USER_ADMIN,
                'number' => 2
            ],
            self::PROJECT_3 => [
                'name' => self::PROJECT_3,
                'call_of_project' => CallOfProjectFixtures::CALL_OF_PROJECT_1,
                'createdBy' => UserFixtures::USER_ADMIN,
                'number' => 3
            ],
            self::PROJECT_4 => [
                'name' => self::PROJECT_4,
                'call_of_project' => CallOfProjectFixtures::CALL_OF_PROJECT_1,
                'createdBy' => UserFixtures::USER_ADMIN,
                'number' => 4
            ],
            self::PROJECT_5 => [
                'name' => self::PROJECT_5,
                'call_of_project' => CallOfProjectFixtures::CALL_OF_PROJECT_1,
                'createdBy' => UserFixtures::USER_ADMIN,
                'number' => 5
            ],
            self::PROJECT_6 => [
                'name' => self::PROJECT_6,
                'call_of_project' => CallOfProjectFixtures::CALL_OF_PROJECT_2,
                'createdBy' => UserFixtures::USER_ADMIN,
                'number' => 1
            ],
            self::PROJECT_7 => [
                'name' => self::PROJECT_7,
                'call_of_project' => CallOfProjectFixtures::CALL_OF_PROJECT_2,
                'createdBy' => UserFixtures::USER_USER1,
                'number' => 2
            ],
            self::PROJECT_8 => [
                'name' => self::PROJECT_8,
                'call_of_project' => CallOfProjectFixtures::CALL_OF_PROJECT_2,
                'createdBy' => UserFixtures::USER_USER1,
                'number' => 3
            ],
            self::PROJECT_9 => [
                'name' => self::PROJECT_9,
                'call_of_project' => CallOfProjectFixtures::CALL_OF_PROJECT_2,
                'createdBy' => UserFixtures::USER_USER1,
                'number' => 4
            ],
            self::PROJECT_10 => [
                'name' => self::PROJECT_10,
                'call_of_project' => CallOfProjectFixtures::CALL_OF_PROJECT_2,
                'createdBy' => UserFixtures::USER_USER1,
                'number' => 5
            ],
            self::PROJECT_11 => [
                'name' => self::PROJECT_11,
                'call_of_project' => CallOfProjectFixtures::CALL_OF_PROJECT_2,
                'createdBy' => UserFixtures::USER_USER1,
                'number' => 6
            ],
            self::PROJECT_12 => [
                'name' => self::PROJECT_12,
                'call_of_project' => CallOfProjectFixtures::CALL_OF_PROJECT_2,
                'createdBy' => UserFixtures::USER_USER1,
                'number' => 7
            ],
        ];

        foreach ($projects as $ref => $projectFixture) {

            $project = new Project();
            $project->setName($projectFixture['name']);
            $project->setCallOfProject($this->getReference(CallOfProjectFixtures::class . $projectFixture['call_of_project']));
            $project->setCreatedBy($this->getReference(UserFixtures::class . $projectFixture['createdBy']));
            $manager->persist($project);

            $this->addReference(self::class . $projectFixture['name'], $project);
            $project->setNumber($projectFixture['number']);
        }

        $manager->flush();
    }

    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        return [
            CallOfProjectFixtures::class,
            UserFixtures::class,
        ];
    }
}

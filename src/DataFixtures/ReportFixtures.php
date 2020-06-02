<?php


namespace App\DataFixtures;


use App\Entity\Report;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ReportFixtures extends Fixture implements DependentFixtureInterface
{

    const REPORT_1 = 'report_1';
    const REPORT_2 = 'report_2';
    const REPORT_3 = 'report_3';
    const REPORT_4 = 'report_4';
    const REPORT_5 = 'report_5';

    public function load(ObjectManager $manager)
    {

        $project1Reports = [
            [
                'name' => self::REPORT_1,
                'user' => UserFixtures::USER_ADMIN,
                'deadline' => new \DateTime()
            ],
            [
                'name' => self::REPORT_2,
                'user' => UserFixtures::USER_USER1,
                'deadline' => new \DateTime('+1 day')
            ],
            [
                'name' => self::REPORT_3,
                'user' => UserFixtures::USER_USER2,
                'deadline' => new \DateTime('+2 day')
            ],
            [
                'name' => self::REPORT_4,
                'user' => UserFixtures::USER_USER3,
                'deadline' => new \DateTime('-1 day')
            ],
        ];

        foreach ($project1Reports as $reportFixtures) {

            $report = new Report();
            $report->setName($reportFixtures['name']);
            $report->setDeadline($reportFixtures['deadline']);
            $report->setCreatedBy($this->getReference(UserFixtures::class . UserFixtures::USER_ADMIN));
            $report->setReporter($this->getReference(UserFixtures::class . $reportFixtures['user']));
            $report->setProject($this->getReference(ProjectFixtures::class . ProjectFixtures::PROJECT_1));
            $manager->persist($report);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            ProjectFixtures::class,
        ];
    }
}
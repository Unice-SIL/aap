<?php


namespace App\DataFixtures;


use App\Entity\OrganizingCenter;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Class UserFixtures
 * @package App\DataFixtures
 */
class OrganizingCenterFixtures extends Fixture
{
    const ORGANIZING_CENTER_1 = 'Centre_organisateur_1';
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $organizingCenter = new OrganizingCenter();
        $organizingCenter->setName(self::ORGANIZING_CENTER_1);
        $manager->persist($organizingCenter);

        $manager->flush();
    }
}
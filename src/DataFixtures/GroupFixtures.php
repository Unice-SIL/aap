<?php


namespace App\DataFixtures;


use App\Entity\Group;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PropertyAccess\PropertyAccess;

class GroupFixtures extends Fixture
{

    const GROUP_MATHS = 'maths';
    const GROUP_LANGUAGES = 'languages';

    public function load(ObjectManager $manager)
    {
        $groups = [
            [
                'name' => self::GROUP_MATHS,
            ],
            [
                'name' => self::GROUP_LANGUAGES,
            ]
        ];

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        foreach ($groups as $groupFixture) {
            $group = new Group();

            foreach ($groupFixture as $property => $value) {
                $propertyAccessor->setValue($group, $property, $value);
            }

            $manager->persist($group);
        }

        $manager->flush();
    }
}
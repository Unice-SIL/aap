<?php


namespace App\DataFixtures;


use App\Entity\Group;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PropertyAccess\PropertyAccess;

class GroupFixtures extends Fixture implements DependentFixtureInterface
{

    const GROUP_MATHS = 'maths';
    const GROUP_LANGUAGES = 'languages';

    public function load(ObjectManager $manager)
    {
        $groups = [
            [
                'name' => self::GROUP_MATHS,
                'members' => [UserFixtures::USER_ADMIN, UserFixtures::USER_USER3]
            ],
            [
                'name' => self::GROUP_LANGUAGES,
            ]
        ];

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        foreach ($groups as $groupFixture) {
            $group = new Group();

            foreach ($groupFixture as $property => $value) {

                if ($property === 'members') {
                    $members = [];
                    foreach ($value as $member) {
                        $members[] = $this->getReference(UserFixtures::class . $member);
                    }

                    $value = $members;
                }
                $propertyAccessor->setValue($group, $property, $value);
            }

            $manager->persist($group);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class
        ];
    }
}
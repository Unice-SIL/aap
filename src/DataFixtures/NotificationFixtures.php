<?php


namespace App\DataFixtures;


use App\Entity\Notification;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class NotificationFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $notification = new Notification();
        $notification->setTitle('Première notification');
        $notification->setRouteName('app.homepage');
        $notification->setUser($this->getReference(UserFixtures::class . UserFixtures::USER_ADMIN));
        $manager->persist($notification);

        $notification = new Notification();
        $notification->setTitle('Deuxième notification');
        $notification->setRouteName('app.homepage');
        $notification->setUser($this->getReference(UserFixtures::class . UserFixtures::USER_ADMIN));
        $manager->persist($notification);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class
        ];
    }
}
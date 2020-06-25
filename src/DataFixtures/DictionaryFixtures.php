<?php

namespace App\DataFixtures;

use App\Entity\Dictionary;
use App\Entity\DictionaryContent;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class DictionaryFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $dictionary = new Dictionary();
        $dictionary->setName('Dictionary 1');
        $dictionary->setCreatedBy($this->getReference(UserFixtures::class . UserFixtures::USER_ADMIN));


        $content1 = (new DictionaryContent())
                    ->setKeyy('key1')
                    ->setValue('value1')
        ;
        $dictionary->addDictionaryContent($content1);

        $content2 = (new DictionaryContent())
            ->setKeyy('key2')
            ->setValue('value2')
        ;
        $dictionary->addDictionaryContent($content2);

        $content3 = (new DictionaryContent())
            ->setKeyy('key3')
            ->setValue('value3')
        ;
        $dictionary->addDictionaryContent($content3);

        $manager->persist($dictionary);
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class
        ];
    }
}

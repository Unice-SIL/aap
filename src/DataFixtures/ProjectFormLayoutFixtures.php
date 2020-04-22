<?php

namespace App\DataFixtures;

use App\Entity\ProjectFormLayout;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProjectFormLayoutFixtures extends Fixture
{
    const FORM_LAYOUT_1 = 'Form_layout_1';
    const FORM_LAYOUT_2 = 'Form_layout_2';

    public function load(ObjectManager $manager)
    {
        /**
         * FormLayout 1
         */
         $projectFormLayout = new ProjectFormLayout();
         $projectFormLayout->setName(self::FORM_LAYOUT_1);

         $this->addReference(self::class . self::FORM_LAYOUT_1, $projectFormLayout);
         $manager->persist($projectFormLayout);

        /**
         * FormLayout 2
         */
        $projectFormLayout = new ProjectFormLayout();
        $projectFormLayout->setName(self::FORM_LAYOUT_2);

        $this->addReference(self::class . self::FORM_LAYOUT_2, $projectFormLayout);
        $manager->persist($projectFormLayout);

        $manager->flush();
    }
}

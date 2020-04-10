<?php

namespace App\DataFixtures;

use App\Entity\ProjectFormLayout;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProjectFormLayoutFixtures extends Fixture
{
    const FORM_LAYOUT_1 = 'Form_layout_1';

    public function load(ObjectManager $manager)
    {
         $projectFormLayout = new ProjectFormLayout();
         $projectFormLayout->setName(self::FORM_LAYOUT_1);

         $this->addReference(self::class . self::FORM_LAYOUT_1, $projectFormLayout);
         $manager->persist($projectFormLayout);

        $manager->flush();
    }
}

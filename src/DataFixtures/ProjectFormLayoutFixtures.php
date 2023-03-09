<?php

namespace App\DataFixtures;

use App\Entity\ProjectFormLayout;
use App\Entity\ProjectFormWidget;
use App\Widget\FormWidget\CheckboxWidget;
use App\Widget\FormWidget\TextWidget;
use App\Widget\FormWidget\UniqueSelectWidget;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProjectFormLayoutFixtures extends Fixture
{
    const FORM_LAYOUT_1 = 'Form_layout_1';
    const FORM_LAYOUT_2 = 'Form_layout_2';
    const FORM_LAYOUT_3 = 'Form_layout_3';

    public function load(ObjectManager $manager)
    {
         $widget1 = new TextWidget();
         $widget1->setLabel('Texte court label');

         $widget2 = new CheckboxWidget();
         $widget2->setLabel('Checkbox label');
         $widget2->setChoices([
             'Option 1',
             'Option 2',
             'Option 3',
         ]);

        $widget3 = new UniqueSelectWidget();
        $widget3->setLabel('Liste déroulante label');
        $widget3->setChoices([
            'Option 1',
            'Option 2',
            'Option 3',
        ]);

        /**
         * FormLayout 1
         */
         $projectFormLayout = new ProjectFormLayout();
         $projectFormLayout->setName(self::FORM_LAYOUT_1)->setTitleFieldLabel('Titre');

         $projectFormWidget1 = new ProjectFormWidget();
         $projectFormWidget1->setPosition(1);
         $projectFormWidget1->setWidget($widget1);
         $projectFormLayout->addProjectFormWidget($projectFormWidget1);

        $projectFormWidget2 = new ProjectFormWidget();
        $projectFormWidget2->setPosition(2);
        $projectFormWidget2->setWidget($widget2);
        $projectFormLayout->addProjectFormWidget($projectFormWidget2);

        $projectFormWidget3 = new ProjectFormWidget();
        $projectFormWidget3->setPosition(3);
        $projectFormWidget3->setWidget($widget3);
        $projectFormLayout->addProjectFormWidget($projectFormWidget3);

         $this->addReference(self::class . self::FORM_LAYOUT_1, $projectFormLayout);
         $manager->persist($projectFormLayout);

        /**
         * FormLayout 2
         */
        $projectFormLayout = new ProjectFormLayout();
        $projectFormLayout->setName(self::FORM_LAYOUT_2)->setTitleFieldLabel('Titre');

        $projectFormWidget = new ProjectFormWidget();
        $projectFormWidget->setPosition(1);
        $projectFormWidget->setWidget($widget1);
        $projectFormLayout->addProjectFormWidget($projectFormWidget);

        $projectFormWidget2 = new ProjectFormWidget();
        $projectFormWidget2->setPosition(2);
        $projectFormWidget2->setWidget($widget2);
        $projectFormLayout->addProjectFormWidget($projectFormWidget2);

        $projectFormWidget3 = new ProjectFormWidget();
        $projectFormWidget3->setPosition(3);
        $projectFormWidget3->setWidget($widget3);
        $projectFormLayout->addProjectFormWidget($projectFormWidget3);

        $this->addReference(self::class . self::FORM_LAYOUT_2, $projectFormLayout);
        $manager->persist($projectFormLayout);

        /**
         * FormLayout 3
         */
        $projectFormLayout = new ProjectFormLayout();
        $projectFormLayout->setName(self::FORM_LAYOUT_3)->setTitleFieldLabel('Titre');
        $projectFormLayout->setIsTemplate(true);

        $projectFormWidget = new ProjectFormWidget();
        $projectFormWidget->setPosition(1);
        $projectFormWidget->setWidget($widget1);
        $projectFormLayout->addProjectFormWidget($projectFormWidget);

        $projectFormWidget2 = new ProjectFormWidget();
        $projectFormWidget2->setPosition(2);
        $projectFormWidget2->setWidget($widget2);
        $projectFormLayout->addProjectFormWidget($projectFormWidget2);

        $projectFormWidget3 = new ProjectFormWidget();
        $projectFormWidget3->setPosition(3);
        $projectFormWidget3->setWidget($widget3);
        $projectFormLayout->addProjectFormWidget($projectFormWidget3);

        $this->addReference(self::class . self::FORM_LAYOUT_3, $projectFormLayout);
        $manager->persist($projectFormLayout);

        $manager->flush();
    }
}

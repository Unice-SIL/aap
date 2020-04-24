<?php

namespace App\Form\Widget;

use App\Entity\Project;
use App\Widget\FormWidget\FormWidgetInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class DynamicWidgetsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $project = $builder->getData();

        $builder->add('name', null, [
            'label' => 'app.project.property.name.label'
        ]);
        
        if (!$project instanceof Project) {
            return;
        }

        foreach ($project->getCallOfProject()->getProjectFormLayout()->getProjectFormWidgets() as $projectFormWidget) {

            $widget = $projectFormWidget->getWidget();

            $type = null;
            $options = [];

            if ($widget instanceof FormWidgetInterface) {

                $type = $widget->getSymfonyType();
                $options = $widget->getOptions();

            }

            $builder->add($projectFormWidget->getPosition(), $type, array_merge($options, ['mapped' => false]));

        }
    }

}
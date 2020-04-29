<?php

namespace App\Form\Widget;

use App\Entity\Project;
use App\Entity\ProjectContent;
use App\Widget\FormWidget\FormWidgetInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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

        $projectContents = $project->getProjectContents();
        $projectFormWidgets = $project->getCallOfProject()->getProjectFormLayout()->getProjectFormWidgets();

        if ($options['allWidgets']) {
            $projectFormWidgets = $project->getCallOfProject()->getProjectFormLayout()->getAllProjectFormWidgets();
        }

        foreach ($projectFormWidgets as $projectFormWidget) {

            $widget = $projectFormWidget->getWidget();

            $type = null;
            $options = [];

            if ($widget instanceof FormWidgetInterface) {

                /** @var ProjectContent $projectContent */
                $projectContent = $projectContents->filter(function ($projectContent) use ($projectFormWidget) {
                    return $projectContent->getProjectFormWidget() === $projectFormWidget;
                })->first();

                if ($projectContent instanceof ProjectContent) {
                    $data = $projectContent->getContent();
                }

                $type = $widget->getSymfonyType();
                $options = $widget->getOptions();
            }

            $builder->add($projectFormWidget->getPosition(), $type, array_merge($options, [
                'mapped' => false,
                'data' => $data ?? null,
            ]));

        }

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'allWidgets' => false
        ]);
    }
}
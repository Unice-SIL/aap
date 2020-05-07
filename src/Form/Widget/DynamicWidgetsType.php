<?php

namespace App\Form\Widget;

use App\Entity\Project;
use App\Entity\ProjectContent;
use App\Form\Type\FileWidgetType;
use App\Widget\FormWidget\FormWidgetInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\File;
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
            $widgetOptions = [];

            if ($widget instanceof FormWidgetInterface) {

                $type = $widget->getSymfonyType();
                $widgetOptions = $widget->getOptions();

                /** @var ProjectContent $projectContent */
                $projectContent = $projectContents->filter(function ($projectContent) use ($projectFormWidget) {
                    return $projectContent->getProjectFormWidget() === $projectFormWidget;
                })->first();

                if ($projectContent instanceof ProjectContent) {
                    $data = $projectContent->getContent();

                    //Special cas if File
                    if ($widget->isFileWidget()) {
                        $data = [
                            'file' => $data,
                            'delete' => false
                        ];

                        $widgetOptions['options'] = $widgetOptions;
                        $widgetOptions['label'] = false;

                        if ($project and $project->getId() and $data['file'] instanceof File) {
                            $widgetOptions['required'] = false;
                        }
                    }


                }


            }

            $builder->add($projectFormWidget->getPosition(), $type, array_merge($widgetOptions, [
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
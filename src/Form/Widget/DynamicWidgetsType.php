<?php

namespace App\Form\Widget;

use App\Entity\Project;
use App\Entity\ProjectContent;
use App\Repository\ProjectRepository;
use App\Widget\FormWidget\FormWidgetInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DynamicWidgetsType extends AbstractType
{
    private $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $project = $builder->getData();
        $titleFielLabel = $project->getCallOfProject()->getProjectFormLayout()->getTitleFieldLabel();
        $titleFielLabel = empty($titleFielLabel) ? 'Nom du champ principal (par défaut: "Titre")' : $titleFielLabel;

        $builder->add('name', null, [
            'label' => $titleFielLabel
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

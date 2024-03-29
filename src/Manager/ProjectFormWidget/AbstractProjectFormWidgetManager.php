<?php


namespace App\Manager\ProjectFormWidget;


use App\Entity\ProjectFormWidget;

abstract class AbstractProjectFormWidgetManager implements ProjectFormWidgetManagerInterface
{
    public function create(): ProjectFormWidget
    {
        return new ProjectFormWidget();
    }

    public function cloneForNewProjectFormLayout(ProjectFormWidget $projectFormWidget): ProjectFormWidget
    {
        $clone = new ProjectFormWidget();
        $clone->setPosition($projectFormWidget->getPosition());
        $clone->setWidget($projectFormWidget->getWidget());
        $clone->setWidgetClass($projectFormWidget->getWidgetClass());
        $clone->setTitle($projectFormWidget->getTitle());

        return $clone;
    }

    public abstract function save(ProjectFormWidget $projectFormWidget);

    public abstract function update(ProjectFormWidget $projectFormWidget);

    public abstract function delete(ProjectFormWidget $projectFormWidget);


}
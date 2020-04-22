<?php


namespace App\Manager\ProjectFormWidget;


use App\Entity\ProjectFormWidget;

abstract class AbstractProjectFormWidgetManager implements ProjectFormWidgetManagerInterface
{
    public function create(): ProjectFormWidget
    {
        return new ProjectFormWidget();
    }

    public abstract function save(ProjectFormWidget $projectFormWidget);

    public abstract function update(ProjectFormWidget $projectFormWidget);

    public abstract function delete(ProjectFormWidget $projectFormWidget);


}
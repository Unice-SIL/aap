<?php


namespace App\Manager\ProjectFormLayout;


use App\Entity\ProjectFormLayout;

abstract class AbstractProjectFormLayoutManager implements ProjectFormLayoutManagerInterface
{
    public function create(): ProjectFormLayout
    {
        return new ProjectFormLayout();
    }

    public abstract function save(ProjectFormLayout $projectFormLayout);

    public abstract function update(ProjectFormLayout $projectFormLayout);

    public abstract function delete(ProjectFormLayout $projectFormLayout);


}
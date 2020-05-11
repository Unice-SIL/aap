<?php


namespace App\Manager\ProjectFormLayout;


use App\Entity\CallOfProject;
use App\Entity\ProjectFormLayout;

abstract class AbstractProjectFormLayoutManager implements ProjectFormLayoutManagerInterface
{
    public function create(?CallOfProject $callOfProject = null): ProjectFormLayout
    {
        $projectFormLayout = new ProjectFormLayout();

        if ($callOfProject) {

            $callOfProject->addProjectFormLayout($projectFormLayout);
            $projectFormLayout->setName($callOfProject->getName() . ' ' . 'formulaire');
        }
        return $projectFormLayout;
    }

    public abstract function save(ProjectFormLayout $projectFormLayout);

    public abstract function update(ProjectFormLayout $projectFormLayout);

    public abstract function delete(ProjectFormLayout $projectFormLayout);


}
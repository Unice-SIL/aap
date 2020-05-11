<?php

namespace App\Manager\ProjectFormLayout;


use App\Entity\CallOfProject;
use App\Entity\ProjectFormLayout;

interface ProjectFormLayoutManagerInterface
{
    public function create(?CallOfProject $callOfProject = null): ProjectFormLayout;

    public function save(ProjectFormLayout $projectFormLayout);

    public function update(ProjectFormLayout $projectFormLayout);

    public function delete(ProjectFormLayout $projectFormLayout);
}
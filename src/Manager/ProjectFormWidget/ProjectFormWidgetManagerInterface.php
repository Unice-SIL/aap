<?php

namespace App\Manager\ProjectFormWidget;


use App\Entity\ProjectFormWidget;

interface ProjectFormWidgetManagerInterface
{
    public function create(): ProjectFormWidget;

    public function cloneForNewProjectFormLayout(ProjectFormWidget $projectFormWidget): ProjectFormWidget;

    public function save(ProjectFormWidget $projectFormWidget);

    public function update(ProjectFormWidget $projectFormWidget);

    public function delete(ProjectFormWidget $projectFormWidget);
}
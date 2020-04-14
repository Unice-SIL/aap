<?php

namespace App\Manager\ProjectFormLayout;


use App\Entity\ProjectFormLayout;

interface ProjectFormLayoutManagerInterface
{
    public function create(): ProjectFormLayout;

    public function save(ProjectFormLayout $projectFormLayout);

    public function update(ProjectFormLayout $projectFormLayout);

    public function delete(ProjectFormLayout $projectFormLayout);
}
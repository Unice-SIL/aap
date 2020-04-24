<?php

namespace App\Manager\ProjectContent;


use App\Entity\ProjectContent;
use App\Entity\ProjectFormWidget;

interface ProjectContentManagerInterface
{
    public function create(ProjectFormWidget $projectFormWidget): ProjectContent;

    public function save(ProjectContent $projectContent);

    public function update(ProjectContent $projectContent);

    public function delete(ProjectContent $projectContent);
}
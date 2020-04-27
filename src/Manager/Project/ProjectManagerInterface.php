<?php

namespace App\Manager\Project;


use App\Entity\CallOfProject;
use App\Entity\Project;

interface ProjectManagerInterface
{
    public function create(CallOfProject $callOfProject): Project;

    public function refreshProjectContents(Project $project): void;

    public function save(Project $project);

    public function update(Project $project);

    public function delete(Project $project);

}
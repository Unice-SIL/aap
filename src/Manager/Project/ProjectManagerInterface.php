<?php

namespace App\Manager\Project;


use App\Entity\CallOfProject;
use App\Entity\Project;
use App\Exception\ProjectTransitionException;

interface ProjectManagerInterface
{
    /**
     * @param Project $project
     * @param string $transition
     * @throws ProjectTransitionException
     * @return mixed
     */
    public function validateOrRefuse(Project $project, string $transition);

    public function create(CallOfProject $callOfProject): Project;

    public function refreshProjectContents(Project $project): void;

    public function save(Project $project);

    public function update(Project $project);

    public function delete(Project $project);

}
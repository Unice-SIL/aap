<?php

namespace App\Manager\CallOfProject;


use App\Entity\CallOfProject;

interface CallOfProjectManagerInterface
{
    public function create(): CallOfProject;

    public function save(CallOfProject $callOfProject);

    public function update(CallOfProject $callOfProject);

    public function delete(CallOfProject $callOfProject);
}
<?php


namespace App\Manager\CallOfProject;


use App\Entity\CallOfProject;

abstract class AbstractCallOfProjectManager implements CallOfProjectManagerInterface
{
    public function create(): CallOfProject
    {
        return new CallOfProject();
    }

    public abstract function save(CallOfProject $callOfProject);

    public abstract function update(CallOfProject $callOfProject);

    public abstract function delete(CallOfProject $callOfProject);


}
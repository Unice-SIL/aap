<?php


namespace App\Manager\Group;


use App\Entity\Group;

abstract class AbstractGroupManager implements GroupManagerInterface
{

    public function create(): Group
    {
        return new Group();
    }

    public abstract function save(Group $group);

    public abstract function update(Group $group);

    public abstract function delete(Group $group);


}
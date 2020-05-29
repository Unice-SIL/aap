<?php

namespace App\Manager\Group;


use App\Entity\Group;

interface GroupManagerInterface
{
    public function create(): Group;

    public function save(Group $group);

    public function update(Group $group);

    public function delete(Group $group);
}
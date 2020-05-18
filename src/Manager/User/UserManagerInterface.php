<?php

namespace App\Manager\User;


use App\Entity\User;

interface UserManagerInterface
{
    public function create(): User;

    public function save(User $user);

    public function update(User $user);

    public function delete(User $user);
}
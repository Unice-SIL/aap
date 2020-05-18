<?php


namespace App\Manager\User;


use App\Entity\User;

abstract class AbstractUserManager implements UserManagerInterface
{

    public function create(): User
    {
        return new User();
    }

    public abstract function save(User $user);

    public abstract function update(User $user);

    public abstract function delete(User $user);


}
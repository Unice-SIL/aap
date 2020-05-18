<?php

namespace App\Manager\Acl;


use App\Entity\Acl;

interface AclManagerInterface
{
    public function create(): Acl;

    public function save(Acl $acl);

    public function update(Acl $user);

    public function delete(Acl $user);
}
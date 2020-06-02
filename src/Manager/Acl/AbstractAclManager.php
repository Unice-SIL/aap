<?php


namespace App\Manager\Acl;


use App\Entity\Acl;
use App\Entity\User;

abstract class AbstractAclManager implements AclManagerInterface
{

    public static function getPermissions(User $user, $entity) {
        return $entity->getAcls()
            ->filter(function ($acl) use ($user) {
                return $acl->getUser() === $user or $user->getGroups()->contains($acl->getGroupe());
            })
            ->map(function ($acl) {
                return $acl->getPermission();
            });
    }

    public function create(): Acl
    {
        return new Acl();
    }

    public abstract function save(Acl $acl);

    public abstract function update(Acl $acl);

    public abstract function delete(Acl $acl);


}
<?php


namespace App\Manager\OrganizingCenter;


use App\Entity\OrganizingCenter;

abstract class AbstractOrganizingCenterManager implements OrganizingCenterManagerInterface
{

    public function create(): OrganizingCenter
    {
        return new OrganizingCenter();
    }

    public abstract function save(OrganizingCenter $organizingCenter);

    public abstract function update(OrganizingCenter $organizingCenter);

    public abstract function delete(OrganizingCenter $organizingCenter);


}
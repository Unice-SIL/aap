<?php

namespace App\Manager\OrganizingCenter;


use App\Entity\OrganizingCenter;

interface OrganizingCenterManagerInterface
{
    public function create(): OrganizingCenter;

    public function save(OrganizingCenter $organizingCenter);

    public function update(OrganizingCenter $organizingCenter);

    public function delete(OrganizingCenter $organizingCenter);
}
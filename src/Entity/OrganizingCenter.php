<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrganizingCenterRepository")
 */
class OrganizingCenter extends Common
{

    const STATUS_INIT = 'init';

    /**
     * OrganizingCenter constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setStatus(self::STATUS_INIT);
    }

}

<?php

namespace App\Traits;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

trait BlameableEntity
{
    /**
     * @var User|null
     *
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $createdBy;

    /**
     * @var User|null
     *
     * @Gedmo\Blameable(on="update")
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $updatedBy;

    /**
     * @return User|null
     */
    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    /**
     * @param User|null $createdBy
     */
    public function setCreatedBy(?User $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    /**
     * @return User|null
     */
    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    /**
     * @param User|null $updatedBy
     */
    public function setUpdatedBy(?User $updatedBy): void
    {
        $this->updatedBy = $updatedBy;
    }

}
<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrganizingCenterRepository")
 */
class OrganizingCenter extends Common
{
    const STATUS_INIT = 'init';

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="organizingCenters")
     */
    private $members;

    /**
     * OrganizingCenter constructor.
     */
    public function __construct()
    {
        $this->setStatus(self::STATUS_INIT);
        $this->members = new ArrayCollection();
    }

    /**
     * @return Collection|User[]
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(User $member): self
    {
        if (!$this->members->contains($member)) {
            $this->members[] = $member;
        }

        return $this;
    }

    public function removeMember(User $member): self
    {
        if ($this->members->contains($member)) {
            $this->members->removeElement($member);
        }

        return $this;
    }


}

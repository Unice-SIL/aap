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

    const EDITOR_PERMISSIONS = [
        Acl::PERMISSION_ADMIN,
        Acl::PERMISSION_MANAGER,
    ];

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CallOfProject", mappedBy="organizingCenter")
     */
    private $callOfProjects;

    /**
     * OrganizingCenter constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setStatus(self::STATUS_INIT);
        $this->callOfProjects = new ArrayCollection();
    }

    /**
     * @return Collection|CallOfProject[]
     */
    public function getCallOfProjects(): Collection
    {
        return $this->callOfProjects;
    }

    public function addCallOfProject(CallOfProject $callOfProject): self
    {
        if (!$this->callOfProjects->contains($callOfProject)) {
            $this->callOfProjects[] = $callOfProject;
            $callOfProject->setOrganizingCenter($this);
        }

        return $this;
    }

    public function removeCallOfProject(CallOfProject $callOfProject): self
    {
        if ($this->callOfProjects->contains($callOfProject)) {
            $this->callOfProjects->removeElement($callOfProject);
            // set the owning side to null (unless already changed)
            if ($callOfProject->getOrganizingCenter() === $this) {
                $callOfProject->setOrganizingCenter(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getName();
    }

}

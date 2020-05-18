<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 */
class Project extends Common
{
    const STATUS_INIT = self::STATUS_WAITING;
    const STATUS_WAITING = 'waiting';
    const STATUS_STUDYING = 'studying';
    const STATUS_REFUSED = 'refused';
    const STATUS_VALIDATED = 'validated';

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CallOfProject", inversedBy="projects")
     * @ORM\JoinColumn(nullable=false)
     */
    private $callOfProject;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProjectContent", mappedBy="project", orphanRemoval=true, cascade={"persist"})
     */
    private $projectContents;

    public function __construct()
    {
        parent::__construct();
        $this->projectContents = new ArrayCollection();
        $this->setStatus(self::STATUS_INIT);
    }

    public function getCallOfProject(): ?CallOfProject
    {
        return $this->callOfProject;
    }

    public function setCallOfProject(?CallOfProject $callOfProject): self
    {
        $this->callOfProject = $callOfProject;

        return $this;
    }

    /**
     * @return Collection|ProjectContent[]
     * @throws \Exception
     */
    public function getProjectContents(): Collection
    {
        $projectContents = $this->projectContents->filter(function ($projectContent) {
            return null !== $projectContent->getProjectFormWidget();
        })->getIterator();

        $projectContents->uasort(function ($first, $second) {
            return (int) $first->getProjectFormWidget()->getPosition() <=> (int) $second->getProjectFormWidget()->getPosition();
        });

        return new ArrayCollection($projectContents->getArrayCopy());
    }

    public function addProjectContent(ProjectContent $projectContent): self
    {
        if (!$this->projectContents->contains($projectContent)) {
            $this->projectContents[] = $projectContent;
            $projectContent->setProject($this);
        }

        return $this;
    }

    public function removeProjectContent(ProjectContent $projectContent): self
    {
        if ($this->projectContents->contains($projectContent)) {
            $this->projectContents->removeElement($projectContent);
            // set the owning side to null (unless already changed)
            if ($projectContent->getProject() === $this) {
                $projectContent->setProject(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getName();
    }
}

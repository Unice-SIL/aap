<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 */
class Project
{
    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CallOfProject", inversedBy="projects")
     * @ORM\JoinColumn(nullable=false)
     */
    private $callOfProject;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProjectContent", mappedBy="project", orphanRemoval=true)
     */
    private $projectContents;

    public function __construct()
    {
        $this->projectContents = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
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
     */
    public function getProjectContents(): Collection
    {
        return $this->projectContents;
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
}

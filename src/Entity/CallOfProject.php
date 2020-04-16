<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CallOfProjectRepository")
 */
class CallOfProject extends Common
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
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Project", mappedBy="callOfProject", orphanRemoval=true)
     */
    private $projects;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProjectFormLayout", mappedBy="callOfProject")
     */
    private $projectFormLayouts;


    public function __construct()
    {
        $this->projects = new ArrayCollection();
        $this->projectFormLayouts = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|Project[]
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
            $project->setCallOfProject($this);
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->contains($project)) {
            $this->projects->removeElement($project);
            // set the owning side to null (unless already changed)
            if ($project->getCallOfProject() === $this) {
                $project->setCallOfProject(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @return Collection|ProjectFormLayout[]
     */
    public function getProjectFormLayouts(): Collection
    {
        return $this->projectFormLayouts;
    }

    public function addProjectFormLayout(ProjectFormLayout $projectFormLayout): self
    {
        if (!$this->projectFormLayouts->contains($projectFormLayout)) {
            $this->projectFormLayouts[] = $projectFormLayout;
            $projectFormLayout->setCallOfProject($this);
        }

        return $this;
    }

    public function removeProjectFormLayout(ProjectFormLayout $projectFormLayout): self
    {
        if ($this->projectFormLayouts->contains($projectFormLayout)) {
            $this->projectFormLayouts->removeElement($projectFormLayout);
            // set the owning side to null (unless already changed)
            if ($projectFormLayout->getCallOfProject() === $this) {
                $projectFormLayout->setCallOfProject(null);
            }
        }

        return $this;
    }

}

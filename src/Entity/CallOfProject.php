<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as AppAssert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CallOfProjectRepository")
 */
class CallOfProject extends Common
{
    const STATUS_INIT = 'init';
    const STATUS_CLOSED = 'closed';
    const STATUS_OPENED = 'opened';
    const STATUS_REVIEW = 'review';
    const STATUS_ARCHIVED = 'archived';

    const EDITOR_PERMISSIONS = [
        Acl::PERMISSION_ADMIN,
        Acl::PERMISSION_MANAGER,
    ];

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Project", mappedBy="callOfProject", orphanRemoval=true)
     */
    private $projects;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank()
     * @Assert\DateTime
     */
    private $startDate;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank()
     * @Assert\DateTime
     * @Assert\Expression("value > this.getStartDate()", message="app.call_of_project.errors.date_bigger_than_start_date")
     */
    private $endDate;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProjectFormLayout", mappedBy="callOfProject", cascade={"persist"})
     */
    private $projectFormLayouts;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\OrganizingCenter", inversedBy="callOfProjects")
     * @Assert\NotBlank()
     * @AppAssert\UserShouldBeAdminOrManagerOnOrganizingCenter()
     * @ORM\JoinColumn(nullable=false)
     */
    private $organizingCenter;


    public function __construct()
    {
        parent::__construct();
        $this->projects = new ArrayCollection();
        $this->projectFormLayouts = new ArrayCollection();
        $this->setStatus(self::STATUS_INIT);
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

    public function getProjectFormLayout(): ProjectFormLayout
    {
        return $this->getProjectFormLayouts()->first();
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

    /**
     * @return \DateTime|null
     */
    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime|null $startDate
     */
    public function setStartDate(?\DateTime $startDate): void
    {
        $this->startDate = $startDate;
    }

    /**
     * @return \DateTime|null
     */
    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    /**
     * @param \DateTime|null $endDate
     */
    public function setEndDate(?\DateTime $endDate): void
    {
        $this->endDate = $endDate;
    }

    public function getStatus(): string
    {
        $currentDate = new \DateTime();
        if ($currentDate < $this->getStartDate()) {
            return self::STATUS_CLOSED;
        } elseif ($this->getStartDate() <= $currentDate and $currentDate <= $this->getEndDate()) {
            return self::STATUS_OPENED;
        } else {
            return self::STATUS_REVIEW;
        }
    }

    public function getOrganizingCenter(): ?OrganizingCenter
    {
        return $this->organizingCenter;
    }

    public function setOrganizingCenter(?OrganizingCenter $organizingCenter): self
    {
        $this->organizingCenter = $organizingCenter;

        return $this;
    }
}

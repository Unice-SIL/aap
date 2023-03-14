<?php

namespace App\Entity;

use DateTime;
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
    const STATUS_FINISHED = 'finished';
    const STATUS_ARCHIVED = 'archived';

    const ADMIN_PERMISSIONS = [
        Acl::PERMISSION_ADMIN,
    ];

    const EDIT_PERMISSIONS = [
        Acl::PERMISSION_ADMIN,
        Acl::PERMISSION_MANAGER,
    ];

    const VIEWER_PERMISSIONS = [
        Acl::PERMISSION_ADMIN,
        Acl::PERMISSION_MANAGER,
        Acl::PERMISSION_VIEWER,
    ];

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Project", mappedBy="callOfProject", orphanRemoval=true)
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $projects;

    /**
     * @var DateTime|null
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank()
     * @Assert\DateTime
     */
    private $startDate;

    /**
     * @var DateTime|null
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank()
     * @Assert\DateTime
     * @Assert\Expression("value > this.getStartDate()", message="app.call_of_project.errors.date_bigger_than_start_date")
     */
    private $endDate;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProjectFormLayout", mappedBy="callOfProject", cascade={"persist"}, orphanRemoval=true)
     */
    private $projectFormLayouts;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\OrganizingCenter", inversedBy="callOfProjects")
     * @Assert\NotBlank()
     * @AppAssert\UserShouldBeAdminOrManagerOnOrganizingCenter()
     * @ORM\JoinColumn(nullable=false)
     */
    private $organizingCenter;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $publicationDate;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default": 1})
     */
    private $public = true;

    /**
     * @ORM\Column(type="boolean", options={"default": 1})
     */
    private $multipleDeposit = true ;

    /**
     * @ORM\OneToMany(targetEntity=CallOfProjectMailTemplate::class, mappedBy="callOfProject", orphanRemoval=true)
     */
    private $callOfProjectMailTemplate;

    /**
     * CallOfProject constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->projects = new ArrayCollection();
        $this->projectFormLayouts = new ArrayCollection();
        $this->setStatus(self::STATUS_INIT);
        $this->callOfProjectMailTemplate = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return $this
     */
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

    /**
     * @param string $status
     * @return Collection
     */
    public function getProjectsByStatus(string $status)
    {
        return $this->getProjects()->filter(function(Project $project) use ($status) {
            return $project->getStatus() === $status;
        });
    }

    /**
     * @return ArrayCollection
     */
    public function getProjectsReports()
    {
        $reports = new ArrayCollection();
        /** @var Project $project */
        foreach ($this->projects as $project)
        {
            /** @var Report $report */
            foreach ($project->getReports() as $report)
            {
                $reports->add($report);
            }
        }
        return $reports;
    }

    /**
     * @param string $status
     * @return ArrayCollection
     */
    public function getProjectsReportsByStatus(string $status)
    {
        return $this->getProjectsReports()->filter(function(Report $report) use ($status) {
            return $report->getStatus() === $status;
        });
    }

    /**
     * @param Project $project
     * @return $this
     */
    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
            $project->setCallOfProject($this);
        }

        return $this;
    }

    /**
     * @param Project $project
     * @return $this
     */
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

    /**
     * @return string|null
     */
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

    /**
     * @return ProjectFormLayout
     */
    public function getProjectFormLayout(): ProjectFormLayout
    {
        return $this->getProjectFormLayouts()->first();
    }

    /**
     * @param ProjectFormLayout $projectFormLayout
     * @return $this
     */
    public function addProjectFormLayout(ProjectFormLayout $projectFormLayout): self
    {
        if (!$this->projectFormLayouts->contains($projectFormLayout)) {
            $this->projectFormLayouts[] = $projectFormLayout;
            $projectFormLayout->setCallOfProject($this);
        }

        return $this;
    }

    /**
     * @param ProjectFormLayout $projectFormLayout
     * @return $this
     */
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
     * @return DateTime|null
     */
    public function getStartDate(): ?DateTime
    {
        return $this->startDate;
    }

    /**
     * @param DateTime|null $startDate
     */
    public function setStartDate(?DateTime $startDate): void
    {
        $this->startDate = $startDate;
    }

    /**
     * @return DateTime|null
     */
    public function getEndDate(): ?DateTime
    {
        return $this->endDate;
    }

    /**
     * @param DateTime|null $endDate
     */
    public function setEndDate(?DateTime $endDate): void
    {
        $this->endDate = $endDate;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        $status = parent::getStatus();
        if ($status === self::STATUS_INIT) {
            $currentDate = new DateTime();
            if ($currentDate < $this->getStartDate()) {
                return self::STATUS_CLOSED;
            } elseif ($this->getStartDate() <= $currentDate and $currentDate <= $this->getEndDate()) {
                return self::STATUS_OPENED;
            } else {
                return self::STATUS_CLOSED;
            }
        }
        return $status;
    }

    /**
     * @return OrganizingCenter|null
     */
    public function getOrganizingCenter(): ?OrganizingCenter
    {
        return $this->organizingCenter;
    }

    /**
     * @param OrganizingCenter|null $organizingCenter
     * @return $this
     */
    public function setOrganizingCenter(?OrganizingCenter $organizingCenter): self
    {
        $this->organizingCenter = $organizingCenter;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getPublicationDate(): ?\DateTimeInterface
    {
        return $this->publicationDate;
    }

    /**
     * @param \DateTimeInterface|null $publicationDate
     * @return $this
     */
    public function setPublicationDate(?\DateTimeInterface $publicationDate): self
    {
        $this->publicationDate = $publicationDate;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPublic(): bool
    {
        return $this->public;
    }

    /**
     * @param bool $public
     * @return CallOfProject
     */
    public function setPublic(bool $public): CallOfProject
    {
        $this->public = $public;
        return $this;
    }

    /**
     * @return bool
     */
    public function isMultipleDeposit(): bool
    {
        return $this-> multipleDeposit;
    }

    /**
     * @param bool $multipleDeposit
     * @return $this
     */
    public function setMultipleDeposit(bool $multipleDeposit): self
    {
        $this->multipleDeposit = $multipleDeposit;

        return $this;
    }

    /**
     * @return Collection<int, CallOfProjectMailTemplate>
     */
    public function getCallOfProjectMailTemplate(): Collection
    {
        return $this->callOfProjectMailTemplate;
    }

    public function addCallOfProjectMailTemplate(CallOfProjectMailTemplate $callOfProjectMailTemplate): self
    {
        if (!$this->callOfProjectMailTemplate->contains($callOfProjectMailTemplate)) {
            $this->callOfProjectMailTemplate[] = $callOfProjectMailTemplate;
            $callOfProjectMailTemplate->setCallOfProject($this);
        }

        return $this;
    }

    public function removeCallOfProjectMailTemplate(CallOfProjectMailTemplate $callOfProjectMailTemplate): self
    {
        if ($this->callOfProjectMailTemplate->removeElement($callOfProjectMailTemplate)) {
            // set the owning side to null (unless already changed)
            if ($callOfProjectMailTemplate->getCallOfProject() === $this) {
                $callOfProjectMailTemplate->setCallOfProject(null);
            }
        }

        return $this;
    }
}

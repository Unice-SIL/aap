<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 * @UniqueEntity(fields={"name", "callOfProject"})
 */
class Project extends Common
{
    const STATUS_INIT = self::STATUS_WAITING;
    const STATUS_WAITING = 'waiting';
    const STATUS_STUDYING = 'studying';
    const STATUS_REFUSED = 'refused';
    const STATUS_VALIDATED = 'validated';

    const TRANSITION_VALIDATE = 'validate';
    const TRANSITION_REFUSE = 'refuse';

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CallOfProject", inversedBy="projects")
     * @ORM\JoinColumn(nullable=false)
     */
    private $callOfProject;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProjectContent", mappedBy="project", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $projectContents;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Report", mappedBy="project", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $reports;

    /**
     * @var null|string
     */
    private $validateRejectMailContent = null;

    /** @var bool */
    private $notifyReporters = true;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="project", orphanRemoval=true, cascade={"remove"})
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $comments;

    public function __construct()
    {
        parent::__construct();
        $this->projectContents = new ArrayCollection();
        $this->setStatus(self::STATUS_INIT);
        $this->reports = new ArrayCollection();
        $this->comments = new ArrayCollection();
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

    /**
     * @return Collection|Report[]
     */
    public function getReports(): Collection
    {
        return $this->reports;
    }

    public function addReport(Report $report): self
    {
        if (!$this->reports->contains($report)) {
            $this->reports[] = $report;
            $report->setProject($this);
        }

        return $this;
    }

    public function removeReport(Report $report): self
    {
        if ($this->reports->contains($report)) {
            $this->reports->removeElement($report);
            // set the owning side to null (unless already changed)
            if ($report->getProject() === $this) {
                $report->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setProject($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getProject() === $this) {
                $comment->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getValidateRejectMailContent(): ?string
    {
        return $this->validateRejectMailContent;
    }

    /**
     * @param string|null $validateRejectMailContent
     * @return Project
     */
    public function setValidateRejectMailContent(?string $validateRejectMailContent): Project
    {
        $this->validateRejectMailContent = $validateRejectMailContent;
        return $this;
    }
}

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReportRepository")
 */
class Report extends Common
{
    const STATUS_TO_COMPLETE = 'to_complete';
    const STATUS_COMPLETE = 'complete';
    const STATUS_FINISHED = 'finished';

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank()
     */
    private $deadline;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="reports")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     */
    private $project;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="reports")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     */
    private $reporter;

    /**
     * Report constructor.
     */
    public function __construct()
    {
        $this->setStatus(self::STATUS_INIT);
    }

    /**
     * @return \DateTime|null
     */
    public function getDeadline(): ?\DateTime
    {
        return $this->deadline;
    }

    /**
     * @param \DateTime|null $deadline
     * @return Report
     */
    public function setDeadline(?\DateTime $deadline): Report
    {
        $this->deadline = $deadline;
        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function getReporter(): ?User
    {
        return $this->reporter;
    }

    public function setReporter(?User $reporter): self
    {
        $this->reporter = $reporter;

        return $this;
    }

    public function getStatus(): string
    {
        if (new \DateTime() > $this->deadline) {
            return self::STATUS_FINISHED;
        }

        if ($this->updatedAt > $this->createdAt) {
            return self::STATUS_COMPLETE;
        }

        return self::STATUS_TO_COMPLETE;
    }

}

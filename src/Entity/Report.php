<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReportRepository")
 * @Vich\Uploadable
 * @Assert\Callback({"App\Validator\Entities\FileNotBlankValidator", "validate"})
 */
class Report extends Common
{
    const STATUS_TO_COMPLETE = 'to_complete';
    const STATUS_COMPLETE = 'complete';
    const STATUS_FINISHED = 'finished';

    const NOTIFY_REPORT = 'notify_report';
    const NOTIFY_REPORTS = 'notify_reports';

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
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="reports", fileNameProperty="report.name", size="report.size", mimeType="report.mimeType", originalName="report.originalName", dimensions="report.dimensions")
     *
     * @var File|null
     */
    private $reportFile;

    /**
     * @ORM\Embedded(class="Vich\UploaderBundle\Entity\File")
     *
     * @var EmbeddedFile
     */
    private $report;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /** @var string */
    private $notifyReporters;

    /**
     * Report constructor.
     */
    public function __construct()
    {
        $this->setStatus(self::STATUS_TO_COMPLETE);
        $this->report = new EmbeddedFile();
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

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|UploadedFile|null $reportFile
     */
    public function setReportFile(?File $reportFile = null)
    {
        $this->reportFile = $reportFile;

        if (null !== $reportFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getReportFile(): ?File
    {
        return $this->reportFile;
    }

    public function setReport(EmbeddedFile $report): void
    {
        $this->report = $report;
    }

    public function getReport(): ?EmbeddedFile
    {
        return $this->report;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return string
     */
    public function getNotifyReporters(): ?string
    {
        return $this->notifyReporters;
    }

    /**
     * @param string $notifyReporters
     */
    public function setNotifyReporters(?string $notifyReporters): void
    {
        $this->notifyReporters = $notifyReporters;
    }
}

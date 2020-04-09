<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectContentRepository")
 */
class ProjectContent
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $textValue;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $numberValue;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateValue;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="projectContents")
     * @ORM\JoinColumn(nullable=false)
     */
    private $project;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ProjectFormWidget")
     * @ORM\JoinColumn(nullable=false)
     */
    private $projectFormWidget;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTextValue(): ?string
    {
        return $this->textValue;
    }

    public function setTextValue(?string $textValue): self
    {
        $this->textValue = $textValue;

        return $this;
    }

    public function getNumberValue(): ?float
    {
        return $this->numberValue;
    }

    public function setNumberValue(?float $numberValue): self
    {
        $this->numberValue = $numberValue;

        return $this;
    }

    public function getDateValue(): ?\DateTimeInterface
    {
        return $this->dateValue;
    }

    public function setDateValue(?\DateTimeInterface $dateValue): self
    {
        $this->dateValue = $dateValue;

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

    public function getProjectFormWidget(): ?ProjectFormWidget
    {
        return $this->projectFormWidget;
    }

    public function setProjectFormWidget(?ProjectFormWidget $projectFormWidget): self
    {
        $this->projectFormWidget = $projectFormWidget;

        return $this;
    }
}

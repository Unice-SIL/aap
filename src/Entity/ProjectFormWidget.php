<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectFormWidgetRepository")
 */
class ProjectFormWidget
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
     * @ORM\Column(type="integer")
     */
    private $position;

    /**
     * @ORM\Column(type="text")
     */
    private $widget;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ProjectFormLayout", inversedBy="projectFormWidgets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $projectFormLayout;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getWidget(): ?string
    {
        return $this->widget;
    }

    public function setWidget(string $widget): self
    {
        $this->widget = $widget;

        return $this;
    }

    public function getProjectFormLayout(): ?ProjectFormLayout
    {
        return $this->projectFormLayout;
    }

    public function setProjectFormLayout(?ProjectFormLayout $projectFormLayout): self
    {
        $this->projectFormLayout = $projectFormLayout;

        return $this;
    }
}

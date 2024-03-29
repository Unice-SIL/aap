<?php

namespace App\Entity;

use App\Widget\WidgetInterface;
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
     * @var string|null
     * @ORM\Column(type="string", length=255)
     */
    private $widgetClass;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $isActive = true;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ProjectFormLayout", inversedBy="projectFormWidgets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $projectFormLayout;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getWidget(): ?WidgetInterface
    {
        return unserialize($this->widget);
    }

    public function setWidget(WidgetInterface $widget): self
    {
        $this->widget = serialize($widget);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getWidgetClass(): ?string
    {
        return $this->widgetClass;
    }

    /**
     * @param string|null $widgetClass
     */
    public function setWidgetClass(?string $widgetClass): void
    {
        $this->widgetClass = $widgetClass;
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

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     */
    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    /**
     * @return bool
     */
    public function isActiveToggle()
    {
        return $this->isActive = !$this->isActive();
    }

    public function __clone()
    {
        $clone = $this;
        $clone->setId(null);
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }
}

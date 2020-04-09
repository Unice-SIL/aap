<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectFormLayoutRepository")
 */
class ProjectFormLayout
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
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isTemplate;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProjectFormWidget", mappedBy="projectFormLayout", orphanRemoval=true)
     */
    private $projectFormWidgets;

    public function __construct()
    {
        $this->projectFormWidgets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getIsTemplate(): ?bool
    {
        return $this->isTemplate;
    }

    public function setIsTemplate(bool $isTemplate): self
    {
        $this->isTemplate = $isTemplate;

        return $this;
    }

    /**
     * @return Collection|ProjectFormWidget[]
     */
    public function getProjectFormWidgets(): Collection
    {
        return $this->projectFormWidgets;
    }

    public function addProjectFormWidget(ProjectFormWidget $projectFormWidget): self
    {
        if (!$this->projectFormWidgets->contains($projectFormWidget)) {
            $this->projectFormWidgets[] = $projectFormWidget;
            $projectFormWidget->setProjectFormLayout($this);
        }

        return $this;
    }

    public function removeProjectFormWidget(ProjectFormWidget $projectFormWidget): self
    {
        if ($this->projectFormWidgets->contains($projectFormWidget)) {
            $this->projectFormWidgets->removeElement($projectFormWidget);
            // set the owning side to null (unless already changed)
            if ($projectFormWidget->getProjectFormLayout() === $this) {
                $projectFormWidget->setProjectFormLayout(null);
            }
        }

        return $this;
    }
}

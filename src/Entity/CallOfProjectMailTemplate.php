<?php

namespace App\Entity;

use App\Repository\CallOfProjectMailTemplateRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CallOfProjectMailTemplateRepository::class)
 */
class CallOfProjectMailTemplate
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isAutomaticSendingValidationMail = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isAutomaticSendingRefusalMail = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $subject;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $body;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=CallOfProject::class, inversedBy="callOfProjectMailTemplate")
     * @ORM\JoinColumn(nullable=false)
     */
    private $callOfProject;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsAutomaticSendingValidationMail(): ?bool
    {
        return $this->isAutomaticSendingValidationMail;
    }

    public function setIsAutomaticSendingValidationMail(bool $isAutomaticSendingValidationMail): self
    {
        $this->isAutomaticSendingValidationMail = $isAutomaticSendingValidationMail;

        return $this;
    }

    public function getIsAutomaticSendingRefusalMail(): ?bool
    {
        return $this->isAutomaticSendingRefusalMail;
    }

    public function setIsAutomaticSendingRefusalMail(bool $isAutomaticSendingRefusalMail): self
    {
        $this->isAutomaticSendingRefusalMail = $isAutomaticSendingRefusalMail;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(?string $body): self
    {
        $this->body = $body;

        return $this;
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

    public function getCallOfProject(): ?CallOfProject
    {
        return $this->callOfProject;
    }

    public function setCallOfProject(?CallOfProject $callOfProject): self
    {
        $this->callOfProject = $callOfProject;

        return $this;
    }
}

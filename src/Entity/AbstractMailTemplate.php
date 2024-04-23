<?php

namespace App\Entity;

use App\Entity\Interfaces\MailTemplateInterface;
use Doctrine\ORM\Mapping as ORM;

class AbstractMailTemplate implements MailTemplateInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $subject;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $body;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false,  options={"default": 1})
     */
    protected $enable = true;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): MailTemplateInterface
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSubject(): ?string
    {
        return $this->subject;
    }

    /**
     * @param string|null $subject
     * @return $this
     */
    public function setSubject(?string $subject): MailTemplateInterface
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * @param string|null $body
     * @return $this
     */
    public function setBody(?string $body): MailTemplateInterface
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEnable(): bool
    {
        return $this->enable;
    }

    /**
     * @param bool $enable
     * @return $this
     */
    public function setEnable(bool $enable): MailTemplateInterface
    {
        $this->enable = $enable;
        return $this;
    }
}
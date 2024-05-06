<?php

namespace App\Entity\Interfaces;

interface MailTemplateInterface
{
    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @param string $name
     * @return self
     */
    public function setName(string $name): self;

    /**
     * @return string|null
     */
    public function getSubject(): ?string;

    /**
     * @param string|null $subject
     * @return self
     */
    public function setSubject(?string $subject): self;

    /**
     * @return string|null
     */
    public function getBody(): ?string;

    /**
     * @param string|null $body
     * @return self
     */
    public function setBody(?string $body): self;

    /**
     * @return bool
     */
    public function isEnable(): bool;

    /**
     * @param bool $enable
     * @return self
     */
    public function setEnable(bool $enable): self;
}
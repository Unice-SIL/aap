<?php


namespace App\Entity;

use App\Traits\BlameableEntity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\MappedSuperclass;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/** @MappedSuperclass */
class Common
{
    use TimestampableEntity;
    use BlameableEntity;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=30)
     */
    private $status;

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return CallOfProject
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

}
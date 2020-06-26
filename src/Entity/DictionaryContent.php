<?php

namespace App\Entity;

use App\Repository\DictionaryContentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=DictionaryContentRepository::class)
 * @UniqueEntity(fields={"code", "dictionary"})
 */
class DictionaryContent
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity=Dictionary::class, inversedBy="dictionaryContents")
     * @ORM\JoinColumn(nullable=false)
     */
    private $dictionary;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getDictionary(): ?Dictionary
    {
        return $this->dictionary;
    }

    public function setDictionary(?Dictionary $dictionary): self
    {
        $this->dictionary = $dictionary;

        return $this;
    }
}

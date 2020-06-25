<?php

namespace App\Entity;

use App\Repository\DictionaryContentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DictionaryContentRepository::class)
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
     */
    private $keyy;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
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

    public function getKeyy(): ?string
    {
        return $this->keyy;
    }

    public function setKeyy(string $keyy): self
    {
        $this->keyy = $keyy;

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

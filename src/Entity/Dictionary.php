<?php

namespace App\Entity;

use App\Repository\DictionaryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DictionaryRepository::class)
 */
class Dictionary extends Common
{
    const STATUS_INIT = "init";
    /**
     * @ORM\OneToMany(targetEntity=DictionaryContent::class, mappedBy="dictionary", orphanRemoval=true, cascade={"persist"})
     */
    private $dictionaryContents;

    public function __construct()
    {
        parent::__construct();
        $this->dictionaryContents = new ArrayCollection();
        $this->setStatus(self::STATUS_INIT);
    }

    /**
     * @return Collection|DictionaryContent[]
     */
    public function getDictionaryContents(): Collection
    {
        return $this->dictionaryContents;
    }

    public function addDictionaryContent(DictionaryContent $dictionaryContent): self
    {
        if (!$this->dictionaryContents->contains($dictionaryContent)) {
            $this->dictionaryContents[] = $dictionaryContent;
            $dictionaryContent->setDictionary($this);
        }

        return $this;
    }

    public function removeDictionaryContent(DictionaryContent $dictionaryContent): self
    {
        if ($this->dictionaryContents->contains($dictionaryContent)) {
            $this->dictionaryContents->removeElement($dictionaryContent);
            // set the owning side to null (unless already changed)
            if ($dictionaryContent->getDictionary() === $this) {
                $dictionaryContent->setDictionary(null);
            }
        }

        return $this;
    }
}

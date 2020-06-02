<?php


namespace App\Entity;

/**
 * Class Help
 * @package App\Entity
 */
class Help
{
    /**
     * @var null|string
     */
    private $label;

    /**
     * @var array
     */
    private $keywords = [];

    /**
     * @var null|string
     */
    private $template;

    /**
     * @var float
     */
    private $score = 0;

    /**
     * @return string|null
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * @param string|null $label
     * @return Help
     */
    public function setLabel(?string $label): Help
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return array
     */
    public function getKeywords(): array
    {
        return $this->keywords;
    }

    /**
     * @param array $keywords
     * @return Help
     */
    public function setKeywords(array $keywords): Help
    {
        $this->keywords = $keywords;
        return $this;
    }

    /**
     * @param string $keyword
     * @return $this
     */
    public function addKeyword(string $keyword)
    {
        if(!in_array($keyword, $this->keywords))
        {
            $this->keywords[] = $keyword;
        }
        return $this;
    }

    /**
     * @param string $keyword
     * @return $this
     */
    public function removeKeyword(string $keyword)
    {
        if($offset = array_search($keyword, $this->keywords))
        {
            unset($this->keywords[$offset]);
        }
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTemplate(): ?string
    {
        return $this->template;
    }

    /**
     * @param string|null $template
     * @return Help
     */
    public function setTemplate(?string $template): Help
    {
        $this->template = $template;
        return $this;
    }

    /**
     * @return string|null
     */
    public function __toString()
    {
        return $this->getLabel();
    }

    /**
     * @return float
     */
    public function getScore(): float
    {
        return $this->score;
    }

    /**
     * @param float $score
     * @return Help
     */
    public function setScore(float $score): Help
    {
        $this->score = $score;
        return $this;
    }

}
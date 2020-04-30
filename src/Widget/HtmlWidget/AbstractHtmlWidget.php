<?php


namespace App\Widget\HtmlWidget;


use App\Widget\AbstractWidget;
use Symfony\Component\Validator\Constraints as Assert;

abstract class AbstractHtmlWidget extends AbstractWidget
{
    /**
     * @var string|null
     * @Assert\NotBlank()
     */
    protected $content;

    /**
     * @var string|null
     */
    protected $style;

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string|null $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return string|null
     */
    public function getStyle(): ?string
    {
        return $this->style;
    }

    /**
     * @param string|null $style
     */
    public function setStyle(?string $style): void
    {
        $this->style = $style;
    }

    public function render(): string
    {
        return '<' . $this->getHtmlTag() . ' style="'. $this->getStyle() .'" >' . $this->getContent() . '</' . $this->getHtmlTag() .'>';
    }

    abstract public function getHtmlTag(): ?string;
}
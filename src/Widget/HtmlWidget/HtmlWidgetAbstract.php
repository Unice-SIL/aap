<?php


namespace App\Widget\HtmlWidget;


use App\Widget\AbstractWidget;
use App\Widget\WidgetInterface;

abstract class HtmlWidgetAbstract extends AbstractWidget
{
    /** @var string|null */
    protected $content;

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
    public function setContent(?string $content): void
    {
        $this->content = $content;
    }

    public function getType(): string
    {
        return WidgetInterface::TYPE_TEXT;
    }

    public function render(): string
    {
        return '<' . $this->getHtmlTag() . '>' . $this->getContent() . '</' . $this->getHtmlTag() .'>';
    }
}
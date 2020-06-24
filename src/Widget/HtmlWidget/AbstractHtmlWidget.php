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


    public function render(): string
    {
        return $this->getContent();
    }

}
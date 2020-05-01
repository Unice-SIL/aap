<?php


namespace App\Widget\HtmlWidget;


use App\Form\Widget\HtmlWidget\HtmlTitleWidgetType;
use Symfony\Component\Validator\Constraints as Assert;

class TitleWidget extends AbstractHtmlWidget implements HtmlWidgetInterface
{
    /**
     * @var string|null
     * @Assert\NotBlank()
     */
    private $htmlTag;

    public function getFormType(): string
    {
        return HtmlTitleWidgetType::class;
    }

    /**
     * @return string|null
     */
    public function getHtmlTag(): ?string
    {
        return $this->htmlTag;
    }

    /**
     * @param string|null $htmlTag
     */
    public function setHtmlTag(?string $htmlTag): void
    {
        $this->htmlTag = $htmlTag;
    }

    public function getChoices()
    {
        return [
          'h1',
          'h2',
          'h3',
          'h4',
          'h5',
          'h6',
        ];
    }

    public function getPosition(): int
    {
        return 1;
    }
}
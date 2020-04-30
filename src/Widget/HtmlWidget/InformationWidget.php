<?php


namespace App\Widget\HtmlWidget;


use App\Form\Widget\HtmlWidget\HtmlInformationWidgetType;

class InformationWidget extends AbstractHtmlWidget implements HtmlWidgetInterface
{
    public function getFormType(): string
    {
        return HtmlInformationWidgetType::class;
    }

    public function getHtmlTag(): ?string
    {
        return 'p';
    }


}
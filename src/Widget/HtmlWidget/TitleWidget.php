<?php


namespace App\Widget\HtmlWidget;


use App\Form\Widget\HtmlWidget\HtmlTitleWidgetType;

class TitleWidget extends HtmlWidgetAbstract implements HtmlWidgetInterface
{
    public function getFormType(): string
    {
        return HtmlTitleWidgetType::class;
    }

    public function getHtmlTag(): string
    {
        return 'h2';
    }
}
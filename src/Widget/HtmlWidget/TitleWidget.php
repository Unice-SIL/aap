<?php


namespace App\Widget\HtmlWidget;


class TitleWidget extends HtmlWidgetAbstract implements HtmlWidgetInterface
{
    const NAME = 'html_title_widget';

    public function getName(): string
    {
        return self::NAME;
    }
}
<?php


namespace App\Widget\HtmlWidget;


use App\Form\Widget\HtmlWidget\HtmlTitleWidgetType;
use App\Widget\WidgetInterface;
use Symfony\Component\Form\FormInterface;

class TitleWidget extends HtmlWidgetAbstract implements HtmlWidgetInterface
{
    public function getFormType(): string
    {
        return HtmlTitleWidgetType::class;
    }

}
<?php


namespace App\Form\Widget\HtmlWidget;


use App\Form\Widget\WidgetType;
use Symfony\Component\Form\AbstractType;

class HtmlWidgetType extends AbstractType
{
    public function getParent()
    {
        return WidgetType::class;
    }
}
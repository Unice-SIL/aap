<?php

namespace App\Form\Widget\HtmlWidget;

use Symfony\Component\Form\AbstractType;

class HtmlInformationWidgetType extends AbstractType
{

    public function getParent()
    {
        return HtmlWidgetType::class;
    }
}
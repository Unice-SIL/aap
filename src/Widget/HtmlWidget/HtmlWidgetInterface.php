<?php

namespace App\Widget\HtmlWidget;

use App\Widget\NoFormWidgetInterface;
use App\Widget\WidgetInterface;

interface HtmlWidgetInterface extends WidgetInterface, NoFormWidgetInterface
{
    public function getHtmlTag(): string;
}
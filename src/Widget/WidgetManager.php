<?php


namespace App\Widget;


class WidgetManager
{
    private $widgets;

    public function __construct()
    {
        $this->widgets = [];
    }

    public function addWidget(WidgetInterface $widget)
    {
        $this->widgets[$widget->getName()] = $widget;
    }

    /**
     * @return array
     */
    public function getWidgets(): array
    {
        return $this->widgets;
    }
}
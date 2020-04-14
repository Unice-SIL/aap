<?php


namespace App\Widget;


use App\Widget\FormWidget\FormWidgetInterface;
use App\Widget\HtmlWidget\HtmlWidgetInterface;

class WidgetManager
{
    private $widgets;
    private $formWidgets;
    private $htmlWidgets;

    public function __construct()
    {
        $this->widgets = [];
        $this->formWidgets = [];
        $this->htmlWidgets = [];
    }

    public function addWidget(WidgetInterface $widget)
    {
        //todo: test unicity of index
        $this->widgets[$widget->getName()] = $widget;
    }

    /**
     * @return array
     */
    public function getWidgets(): array
    {
        return $this->widgets;
    }

    public function getWidget(?string $name): ?WidgetInterface
    {
        if (isset($this->getWidgets()[$name])) {
            return $this->getWidgets()[$name];
        }

        return null;
    }

    public function addFormWidget(FormWidgetInterface $formWidget)
    {
        //todo: test unicity of index
        $this->formWidgets[$formWidget->getName()] = $formWidget;
    }

    /**
     * @return array
     */
    public function getFormWidgets(): array
    {
        return $this->formWidgets;
    }

    public function addHtmlWidget(HtmlWidgetInterface $htmlWidgets)
    {
        //todo: test unicity of index
        $this->htmlWidgets[$htmlWidgets->getName()] = $htmlWidgets;
    }

    /**
     * @return array
     */
    public function getHtmlWidgets(): array
    {
        return $this->htmlWidgets;
    }
}
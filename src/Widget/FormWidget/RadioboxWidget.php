<?php


namespace App\Widget\FormWidget;

class RadioboxWidget extends AbstractChoiceWidget implements FormWidgetInterface
{
    protected function configureOptions(): array
    {
        parent::configureOptions();
        $this->addOptions([
            'expanded' => true,
        ]);

        return $this->options;
    }

}
<?php


namespace App\Widget\FormWidget;

class RadioboxWidget extends AbstractChoiceWidget implements FormWidgetInterface
{
    protected function configureOptions(): void
    {
        parent::configureOptions();
        $this->addOptions([
            'expanded' => true,
        ]);
    }

}
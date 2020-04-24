<?php


namespace App\Widget\FormWidget;

class CheckboxWidget extends AbstractChoiceWidget implements FormWidgetInterface
{

    protected function configureOptions(): void
    {
        parent::configureOptions();
        $this->addOptions([
            'expanded' => true,
            'multiple' => true,
        ]);
    }

}
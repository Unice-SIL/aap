<?php


namespace App\Widget\FormWidget;

class CheckboxWidget extends AbstractChoiceWidget implements FormWidgetInterface
{

    protected function configureOptions(): array
    {
        parent::configureOptions();
        $this->addOptions([
            'expanded' => true,
            'multiple' => true,
        ]);

        return $this->options;
    }

}
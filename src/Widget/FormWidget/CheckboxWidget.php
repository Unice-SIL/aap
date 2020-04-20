<?php


namespace App\Widget\FormWidget;

use App\Form\Widget\FormWidget\FormChoiceWidgetType;

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
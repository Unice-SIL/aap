<?php


namespace App\Widget\FormWidget;

use App\Form\Widget\FormWidget\FormChoiceWidgetType;

class RadioboxWidget extends AbstractChoiceWidget implements FormWidgetInterface
{
    public function getFormType(): string
    {
        return FormChoiceWidgetType::class;
    }

    protected function configureOptions(): void
    {
        parent::configureOptions();
        $this->addOptions([
            'expanded' => true,
        ]);
    }

}
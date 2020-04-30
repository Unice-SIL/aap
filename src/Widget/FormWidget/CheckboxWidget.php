<?php


namespace App\Widget\FormWidget;

use App\Form\Widget\Validation\CheckboxWidgetValidationType;

class CheckboxWidget extends AbstractChoiceWidget implements FormWidgetInterface
{
    public function getOptions(): array
    {
        return array_merge_recursive(parent::getOptions(), [
            'expanded' => true,
            'multiple' => true,
        ]);
    }

    public function getDynamicConstraintsType(): ?string
    {
        return CheckboxWidgetValidationType::class;
    }


}
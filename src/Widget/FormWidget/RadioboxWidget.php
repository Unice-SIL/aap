<?php


namespace App\Widget\FormWidget;

class RadioboxWidget extends AbstractChoiceWidget implements FormWidgetInterface
{
    public function getOptions(): array
    {
        return array_merge_recursive(parent::getOptions(), [
            'expanded' => true,
        ]);
    }

    public function getPosition(): int
    {
        return 6;
    }
}
<?php


namespace App\Widget\FormWidget;


class UniqueSelectWidget extends AbstractChoiceWidget implements FormWidgetInterface
{
    public function getPosition(): int
    {
        return 5;
    }
}
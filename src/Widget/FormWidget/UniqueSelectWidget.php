<?php


namespace App\Widget\FormWidget;


use App\Form\Widget\FormWidget\FormChoiceWidgetType;
use App\Form\Widget\FormWidget\FormUniqueSelectWidgetType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UniqueSelectWidget extends AbstractChoiceWidget implements FormWidgetInterface
{
    public function getFormType(): string
    {
        return FormChoiceWidgetType::class;
    }

}
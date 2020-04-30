<?php


namespace App\Widget\FormWidget;


use App\Form\Type\DatePickerType;
use App\Form\Widget\FormWidget\FormDateWidgetType;

class DateWidget extends AbstractFormWidget implements FormWidgetInterface
{

    public function getFormType(): string
    {
        return FormDateWidgetType::class;
    }

    public function getSymfonyType(): string
    {
        return DatePickerType::class;
    }

    public function transformData($value)
    {
        return unserialize($value);
    }

    /**
     * @param $value \DateTime
     * @return string
     */
    public function reverseTransformData($value)
    {
        return serialize($value);
    }


}
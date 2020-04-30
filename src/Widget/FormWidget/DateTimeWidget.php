<?php


namespace App\Widget\FormWidget;


use App\Form\Type\DateTimePickerType;
use App\Form\Widget\FormWidget\FormDateTimeWidgetType;

class DateTimeWidget extends AbstractFormWidget implements FormWidgetInterface
{

    public function getFormType(): string
    {
        return FormDateTimeWidgetType::class;
    }

    public function getSymfonyType(): string
    {
        return DateTimePickerType::class;
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
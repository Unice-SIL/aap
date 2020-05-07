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

    public function transformData($value, array $options = [])
    {
        return unserialize($value);
    }

    /**
     * @param $value \DateTime
     * @param array $options
     * @return string
     */
    public function reverseTransformData($value, array $options = [])
    {
        return serialize($value);
    }

    public function getPosition(): int
    {
        return 9;
    }
}
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
        $value->onlyDate = true;
        return serialize($value);
    }

    public function getPosition(): int
    {
        return 8;
    }
}
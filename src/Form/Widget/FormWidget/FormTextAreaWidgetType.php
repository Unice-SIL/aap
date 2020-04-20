<?php


namespace App\Form\Widget\FormWidget;


use Symfony\Component\Form\AbstractType;

class FormTextAreaWidgetType extends AbstractType
{
    public function getParent()
    {
        return FormWidgetType::class;
    }
}
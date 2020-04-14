<?php


namespace App\Form\Widget\FormWidget;


use Symfony\Component\Form\AbstractType;

class FormTextWidgetType extends AbstractType
{
    public function getParent()
    {
        return FormWidgetType::class;
    }
}
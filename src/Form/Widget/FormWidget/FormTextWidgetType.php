<?php


namespace App\Form\Widget\FormWidget;

use App\Form\Widget\Validation\TextWidgetValidationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class FormTextWidgetType extends AbstractType
{

    public function getParent()
    {
        return FormWidgetType::class;
    }
}
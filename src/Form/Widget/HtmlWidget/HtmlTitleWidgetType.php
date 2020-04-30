<?php

namespace App\Form\Widget\HtmlWidget;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class HtmlTitleWidgetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $widget = $builder->getData();
        $builder->add('htmlTag', ChoiceType::class, [
            'choices' => array_combine($widget->getChoices(), $widget->getChoices()),
            'label' => 'app.form.widget.html_tag.label',
        ]);
    }

    public function getParent()
    {
        return HtmlWidgetType::class;
    }
}
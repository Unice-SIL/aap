<?php

namespace App\Form\Widget\FormWidget;

use App\Form\Widget\WidgetType;
use App\Widget\FormWidget\FormWidgetInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class FormWidgetType  extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('style', null, [
                'label' => 'app.form.widget.style.label',
                'required' => false
            ])
            ->add('visibilityLabel', CheckboxType::class, [
                'label' => 'app.form.widget.visibility_label.label',
                'required' => false,
                'label_attr' => [
                    'class' => 'text-bold'
                ]
            ])
            ->add('required', CheckboxType::class, [
                'label' => 'app.form.widget.required.label',
                'required' => false,
                'label_attr' => [
                    'class' => 'text-bold'
                ]
            ])
            ->add('placeholder', null, [
                'label' => 'app.form.widget.placeholder.label',
                'required' => false
            ])
        ;

        $widget = $builder->getData();

        if ($widget instanceof  FormWidgetInterface and $widget->getDynamicConstraintsType()) {
            $builder->add('dynamicConstraints', $widget->getDynamicConstraintsType());
        }
    }

    public function getParent()
    {
        return WidgetType::class;
    }

}
<?php

namespace App\Form\Widget\FormWidget;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class FormWidgetType  extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label')
            ->add('required', CheckboxType::class, [
                'required' => false
            ])
        ;
    }

}
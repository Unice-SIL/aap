<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DatePickerType extends AbstractType
{
    public function getParent()
    {
        return DateType::class;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $attr = $view->vars['attr'];

        $class = isset($attr['class']) ? $attr['class'].' ' : '';
        $class .= 'datepicker-input';
        $attr['class'] = $class;

        $autocomplete = isset($attr['autocomplete']) ? $attr['autocomplete'] : 'off';
        $attr['autocomplete'] = $autocomplete;

        $view->vars['attr'] = $attr;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'widget' => 'single_text',
            'format' => 'dd/MM/yyyy',
        ]);
    }

    public function getBlockPrefix(){
         return 'date_time_picker';
    }

}
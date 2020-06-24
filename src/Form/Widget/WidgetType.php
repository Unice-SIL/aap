<?php


namespace App\Form\Widget;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class WidgetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('label', null, [
                'label' => 'app.form.widget.label.label',
                'required' => true,
            ])
        ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $attr = $view->vars['attr'];

        $class = isset($attr['class']) ? $attr['class'].' ' : '';
        $class .= 'form-widget';
        $attr['class'] = $class;

        $view->vars['attr'] = $attr;
    }
}
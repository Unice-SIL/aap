<?php


namespace App\Form\Widget\Validation;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ValidationType extends AbstractType
{
    public function getBlockPrefix()
    {
        return 'app_widget_validation';
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $labelAttr = $view->vars['label_attr'];

        $class = isset($labelAttr['class']) ? $labelAttr['class'].' ' : '';
        $class .= 'text-bold';
        $labelAttr['class'] = $class;

        $view->vars['label_attr'] = $labelAttr;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => 'app.form.widget.dynamic_constraints.label',
        ]);
    }
}
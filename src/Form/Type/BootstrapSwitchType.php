<?php


namespace App\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BootstrapSwitchType extends AbstractType
{
    public function getParent()
    {
        return CheckboxType::class;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $class = $view->vars['attr']['class'] ?? '';
        $class .= 'bootstrap-switch';

        $view->vars['attr']['class'] = $class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'required' => false,
        ]);
    }
}
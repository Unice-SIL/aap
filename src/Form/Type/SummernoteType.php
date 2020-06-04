<?php


namespace App\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class SummernoteType extends AbstractType
{
    public function getParent()
    {
        return TextareaType::class;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $class = $view->vars['attr']['class'] ?? '';
        $class .= 'summernote';

        $view->vars['attr']['class'] = $class;
    }
}
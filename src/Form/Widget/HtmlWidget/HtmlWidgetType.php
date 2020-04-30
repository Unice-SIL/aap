<?php


namespace App\Form\Widget\HtmlWidget;


use App\Form\Widget\WidgetType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class HtmlWidgetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('content', null, [
            'label' => 'app.form.widget.content.label',
            'required' => true,
        ])
        ;
    }

    public function getParent()
    {
        return WidgetType::class;
    }
}
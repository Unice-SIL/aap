<?php


namespace App\Form\Widget\HtmlWidget;


use App\Form\Type\SummernoteType;
use App\Form\Widget\WidgetType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class HtmlWidgetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('content', SummernoteType::class, [
            'label' => 'app.form.widget.content.label',
            'required' => true,
        ])
            ->remove('style')
        ;
    }

    public function getParent()
    {
        return WidgetType::class;
    }
}
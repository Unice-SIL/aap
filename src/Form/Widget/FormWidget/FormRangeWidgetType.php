<?php


namespace App\Form\Widget\FormWidget;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class FormRangeWidgetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('min', IntegerType::class);
        $builder->add('max', IntegerType::class);
        $builder->remove('placeholder', IntegerType::class);
    }


    public function getParent()
    {
        return FormWidgetType::class;
    }
}
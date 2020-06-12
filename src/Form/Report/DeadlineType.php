<?php


namespace App\Form\Report;


use App\Form\Type\DateTimePickerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class DeadlineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('deadline', DateTimePickerType::class, [
                'label' => 'app.report.property.dead_line.label'
            ])
        ;
    }

}
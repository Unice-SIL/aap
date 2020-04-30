<?php


namespace App\Form\Widget\Validation;

use App\Form\Widget\Validation\Constraint\CountConstraintType;
use Symfony\Component\Form\FormBuilderInterface;

class CheckboxWidgetValidationType extends ValidationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('count', CountConstraintType::class)
        ;
    }

}
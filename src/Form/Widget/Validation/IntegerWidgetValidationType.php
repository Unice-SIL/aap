<?php


namespace App\Form\Widget\Validation;


use App\Form\Widget\Validation\Constraint\EmailConstraintType;
use App\Form\Widget\Validation\Constraint\GreaterThanOrEqualConstraintType;
use App\Form\Widget\Validation\Constraint\LengthConstraintType;
use App\Form\Widget\Validation\Constraint\LessThanOrEqualConstraintType;
use App\Form\Widget\Validation\Constraint\RegexConstraintType;
use App\Form\Widget\Validation\Constraint\UrlConstraintType;
use Symfony\Component\Form\FormBuilderInterface;

class IntegerWidgetValidationType extends ValidationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('greaterThanOrEqual', GreaterThanOrEqualConstraintType::class)
            ->add('lessThanOrEqual', LessThanOrEqualConstraintType::class)
        ;
    }

}
<?php


namespace App\Form\Widget\Validation;


use App\Form\Widget\Validation\Constraint\EmailConstraintType;
use App\Form\Widget\Validation\Constraint\LengthConstraintType;
use App\Form\Widget\Validation\Constraint\RegexConstraintType;
use App\Form\Widget\Validation\Constraint\UrlConstraintType;
use Symfony\Component\Form\FormBuilderInterface;

class TextWidgetValidationType extends ValidationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('length', LengthConstraintType::class)
            ->add('email', EmailConstraintType::class)
            ->add('url', UrlConstraintType::class)
            ->add('regex', RegexConstraintType::class)
        ;
    }

}
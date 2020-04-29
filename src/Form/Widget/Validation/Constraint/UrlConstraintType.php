<?php


namespace App\Form\Widget\Validation\Constraint;


use App\Widget\Constraint\UrlConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UrlConstraintType extends AbstractType
{

    public function getParent()
    {
        return ConstraintType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UrlConstraint::class,
            'label'=> 'app.form.widget.validation.url.label'
        ]);
    }
}
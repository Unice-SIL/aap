<?php


namespace App\Form\Widget\Validation\Constraint;


use App\Widget\Constraint\EmailConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmailConstraintType extends AbstractType
{

    public function getParent()
    {
        return ConstraintType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EmailConstraint::class,
            'label'=> 'app.form.widget.validation.email.label'
        ]);
    }
}
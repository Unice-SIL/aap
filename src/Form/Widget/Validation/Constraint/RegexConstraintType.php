<?php


namespace App\Form\Widget\Validation\Constraint;

use App\Widget\Constraint\RegexConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegexConstraintType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pattern')
        ;
    }

    public function getParent()
    {
        return ConstraintType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RegexConstraint::class,
            'label'=> 'app.form.widget.validation.regex.label'
        ]);
    }
}
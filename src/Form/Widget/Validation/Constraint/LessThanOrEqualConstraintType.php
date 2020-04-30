<?php


namespace App\Form\Widget\Validation\Constraint;

use App\Widget\Constraint\LessThanOrEqualConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LessThanOrEqualConstraintType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('value', IntegerType::class, [
                'required' => false
            ])
        ;
    }

    public function getParent()
    {
        return ConstraintType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LessThanOrEqualConstraint::class,
            'label'=> 'app.form.widget.validation.less_than_or_equal.label'
        ]);
    }
}
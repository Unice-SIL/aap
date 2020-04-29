<?php


namespace App\Form\Widget\Validation\Constraint;


use App\Widget\Constraint\LengthConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LengthConstraintType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('min', IntegerType::class, [
                'required' => false
            ])
            ->add('max', IntegerType::class, [
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
            'data_class' => LengthConstraint::class,
            'label'=> 'app.form.widget.validation.length.label'
        ]);
    }
}
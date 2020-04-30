<?php


namespace App\Form\Widget\Validation\Constraint;


use App\Widget\Constraint\CountConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CountConstraintType extends AbstractType
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
            'data_class' => CountConstraint::class,
            'label'=> 'app.form.widget.validation.count.label'
        ]);
    }
}
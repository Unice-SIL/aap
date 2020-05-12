<?php


namespace App\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InitProjectChoiceType extends AbstractType
{
    const NO_INIT = 0;
    const INIT_BY_CALL_OF_PROJECT = 1;
    const INIT_BY_PROJECT_FORM_LAYOUT = 2;

    const CHOICES = [
        'app.call_of_project.init.choices.no_init' => self::NO_INIT,
        'app.call_of_project.init.choices.init_by_call_of_project' => self::INIT_BY_CALL_OF_PROJECT,
        'app.call_of_project.init.choices.init_by_project_form_layout' => self::INIT_BY_PROJECT_FORM_LAYOUT
    ];

    public function getParent()
    {
        return ChoiceType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults( [
            'label' => 'app.call_of_project.init.choices.label',
            'mapped' => false,
            'choices' => self::CHOICES
        ]);
    }
}
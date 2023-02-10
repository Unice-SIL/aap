<?php


namespace App\Form\Project;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class ProjectToStudyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('submit', SubmitType::class, [
            'label' => 'app.project.workflow.to_study.button',
            'attr' => [
                'id' => "btn-launch-projects-review",
                'class' => 'btn btn-primary'
            ]
        ]);
    }
}

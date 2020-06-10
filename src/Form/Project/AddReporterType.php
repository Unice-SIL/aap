<?php


namespace App\Form\Project;


use App\Form\Type\ReportFromUserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class AddReporterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reports', ReportFromUserType::class)
            ->add('notifyReporters', CheckboxType::class, [
                'label' => 'app.report.notify_reporters_by_mail',
                'required' => false
            ])
        ;
    }
}
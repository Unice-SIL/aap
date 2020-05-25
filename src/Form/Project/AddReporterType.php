<?php


namespace App\Form\Project;


use App\Form\Type\ReportFromUserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AddReporterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reports', ReportFromUserType::class)
        ;
    }
}
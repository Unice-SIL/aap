<?php

namespace App\Form\Widget;

use App\Entity\ProjectFormLayout;
use App\Widget\FormWidget\FormWidgetInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DynamicWidgetsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $projectFormLayout = $options['project_form_layout'];
        
        if (!$projectFormLayout instanceof ProjectFormLayout) {
            return;
        }


        foreach ($projectFormLayout->getProjectFormWidgets() as $projectFormWidget) {

            $widget = unserialize($projectFormWidget->getWidget());

            if ($widget instanceof FormWidgetInterface) {
                $builder->add($projectFormWidget->getPosition(), $widget->getSymfonyType(), $widget->getOptions());
                continue;
            }

            $builder->add($projectFormWidget->getPosition());

        }
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'project_form_layout' => null
        ]);
    }
}
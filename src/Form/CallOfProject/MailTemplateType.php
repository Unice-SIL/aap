<?php


namespace App\Form\CallOfProject;


use App\Form\Type\BootstrapSwitchType;
use App\Form\Type\SummernoteType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class MailTemplateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('subject', TextType::class, [
                'label' => 'app.call_of_project.mail_template.property.subject.label'
            ])
            ->add('body', SummernoteType::class, [
                'label' => 'app.call_of_project.mail_template.property.body.label'
            ])
            ->add('enable', BootstrapSwitchType::class, [
                'label' => 'app.call_of_project.mail_template.property.enable.label'
            ])
        ;
    }
}
<?php


namespace App\Form\CallOfProject;


use App\Form\Type\BootstrapSwitchType;
use App\Form\Type\SummernoteType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class MailTemplateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('validationMailTemplate', SummernoteType::class, [
                'label' => 'app.call_of_project.property.validation_mail_template.label'
            ])
            ->add('isAutomaticSendingValidationMail', BootstrapSwitchType::class, [
                'label' => 'app.call_of_project.property.is_automatic_sending_validation_mail.label'
            ])
            ->add('refusalMailTemplate', SummernoteType::class, [
                'label' => 'app.call_of_project.property.refusal_mail_template.label'
            ])
            ->add('notificationToCreatorNewProjectMailTemplate', SummernoteType::class, [
                'label' => 'app.call_of_project.property.notification_to_creator_new_project_mail_template.label'
            ])
            ->add('isAutomaticSendingRefusalMail', BootstrapSwitchType::class, [
                'label' => 'app.call_of_project.property.is_automatic_sending_refusal_mail.label'
            ])
        ;
    }
}

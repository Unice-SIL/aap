<?php


namespace App\Form\CallOfProject;

use App\entity\CallOfProjectMailTemplate;
use App\Constant\MailTemplate;
use App\Form\Type\BootstrapSwitchType;
use App\Form\Type\SummernoteType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CallOfProjectMailTemplateType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('subject', TextType::class, [
                'label' =>'app.mail_template.property.subject.label'
            ])
            ->add('body', SummernoteType::class, [
                'label' => 'app.mail_template.property.body.label'
            ]);
        if ($options['data']->getName() === MailTemplate::VALIDATION_PROJECT) {
            $builder->add('isAutomaticSendingValidationMail', BootstrapSwitchType::class, [
                'label' => 'app.call_of_project.property.is_automatic_sending_validation_mail.label'
            ]);
        }
        if ($options['data']->getName() === MailTemplate::REFUSAL_PROJECT) {
            $builder->add('isAutomaticSendingRefusalMail', BootstrapSwitchType::class, [
                'label' => 'app.call_of_project.property.is_automatic_sending_refusal_mail.label'
            ]);
        }
        $builder->add('Enregistrer', SubmitType::class, [
            'attr' => ['class' => 'btn btn-primary mr-3']
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => CallOfProjectMailTemplate::class
        ));
    }
}

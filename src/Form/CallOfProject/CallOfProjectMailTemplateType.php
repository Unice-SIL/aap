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

    private const MAIL_TEMPLATE_WITH_AUTO_SENDING_MAIL = [
        MailTemplate::VALIDATION_PROJECT,
        MailTemplate::REFUSAL_PROJECT
    ];

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $callOfProjectMailTemplate = $builder->getData();
        $builder
            ->add('subject', TextType::class, [
                'label' =>'app.mail_template.property.subject.label'
            ])
            ->add('body', SummernoteType::class, [
                'label' => 'app.mail_template.property.body.label'
            ]);
        if (in_array($callOfProjectMailTemplate->getName(), self::MAIL_TEMPLATE_WITH_AUTO_SENDING_MAIL)) {
            $builder->add('isAutomaticSendingMail', BootstrapSwitchType::class, [
                'label' => 'app.call_of_project.property.is_automatic_sending_'. substr($callOfProjectMailTemplate->getName(),18 ,7 )   .'_mail.label'
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

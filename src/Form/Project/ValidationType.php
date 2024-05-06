<?php


namespace App\Form\Project;

use App\Constant\MailTemplate;
use App\Entity\Interfaces\MailTemplateInterface;
use App\Entity\Project;
use App\Form\Type\BootstrapSwitchType;
use App\Form\Type\SummernoteType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ValidationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('action', HiddenType::class, [
                'data' => $options['context'],
                'mapped' => false
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {

                $form = $event->getForm();

                /** @var Project $project */
                $project = $event->getData();

                if ($options['context'] === Project::TRANSITION_VALIDATE) {
                    $mailTemplate = $project->getCallOfProject()->getMailTemplate(MailTemplate::NOTIFICATION_USER_VALIDATION_PROJECT);
                } elseif ($options['context'] === Project::TRANSITION_REFUSE) {
                    $mailTemplate = $project->getCallOfProject()->getMailTemplate(MailTemplate::NOTIFICATION_USER_REFUSAL_PROJECT);
                }

                if (!$mailTemplate instanceof MailTemplateInterface) return;

                $automaticSendingData = $mailTemplate->isEnable();

                $builder = $form
                    ->getConfig()
                    ->getFormFactory()
                    ->createNamedBuilder('automaticSending', BootstrapSwitchType::class, null, [
                        'auto_initialize' => false, // it's important!!!
                        'mapped' => false,
                        'data' => $automaticSendingData,
                        'attr' => [
                            'class' => 'automatic-sending-switch',
                        ],
                        'label' => 'app.project.validation_form.automatic_sending.label',
                    ]);

                $formTransformer = function (FormInterface $form, bool $automaticSending) use ($options, $mailTemplate) {
                    $validationForm = $form->getParent();

                    if ($automaticSending) {
                        $validationForm->add('mailTemplate', SummernoteType::class, [
                            'mapped' => false,
                            'data' => $mailTemplate->getBody(),
                            'required' => false
                        ]);
                    } else {
                        $validationForm->remove('mailTemplate');
                    }
                };

                $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($formTransformer) {
                    $form = $event->getForm();
                    /** @var Project $project */
                    $automaticSending = $event->getData();

                    $formTransformer($form, $automaticSending);
                });

                $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($formTransformer) {
                    $form = $event->getForm();
                    $automaticSending = $form->getData();

                    $formTransformer($form, $automaticSending);

                });
                $form->add($builder->getForm());
            });
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $class = $view->vars['attr']['class'] ?? '';
        $class .= ' validation-form';

        $view->vars['attr']['class'] = $class;
        $view->vars['name'] = 'validation_' . $options['context'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('context');
    }
}

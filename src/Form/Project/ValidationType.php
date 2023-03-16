<?php


namespace App\Form\Project;

use App\Constant\MailTemplate;
use App\Entity\Project;
use App\Repository\CallOfProjectMailTemplateRepository;
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

    /** @var CallOfProjectMailTemplateRepository * */
    private $callOfProjectMailTemplateRepository;

    public function __construct(CallOfProjectMailTemplateRepository $callOfProjectMailTemplateRepository)
    {
        $this->callOfProjectMailTemplateRepository = $callOfProjectMailTemplateRepository;
    }

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

                if ($options['context'] === Project::STATUS_VALIDATED) {

                    $callOfProjectMailTemplateValidated = $this->callOfProjectMailTemplateRepository->findOneBy([
                        "name" => MailTemplate::VALIDATION_PROJECT,
                        "callOfProject" => $project->getCallOfProject()
                    ]);
                    $automaticSendingData = $callOfProjectMailTemplateValidated->getIsAutomaticSendingMail();
                } elseif ($options['context'] === Project::STATUS_REFUSED) {
                    $callOfProjectMailTemplateRefusal = $this->callOfProjectMailTemplateRepository->findOneBy([
                        "name" => MailTemplate::REFUSAL_PROJECT,
                        "callOfProject" => $project->getCallOfProject()
                    ]);
                    $automaticSendingData = $callOfProjectMailTemplateRefusal->getIsAutomaticSendingMail();
                }
                $builder = $form
                    ->getConfig()
                    ->getFormFactory()
                    ->createNamedBuilder('automaticSending', BootstrapSwitchType::class, null, array(
                        'auto_initialize' => false, // it's important!!!
                        'mapped' => false,
                        'data' => $automaticSendingData,
                        'attr' => [
                            'class' => 'automatic-sending-switch',
                        ],
                        'label' => 'app.project.validation_form.automatic_sending.label',
                    ));

                $formTransformer = function (FormInterface $form, bool $automaticSending) use ($options) {

                    $validationForm = $form->getParent();

                    if ($automaticSending) {
                        if ($options['context'] === Project::STATUS_VALIDATED) {
                            $mailTemplateData = $this->callOfProjectMailTemplateRepository->findOneBy([
                                "name" => MailTemplate::VALIDATION_PROJECT,
                                "callOfProject" => $validationForm->getData()->getCallOfProject()
                            ]);
                        } elseif ($options['context'] === Project::STATUS_REFUSED) {
                            $mailTemplateData = $this->callOfProjectMailTemplateRepository->findOneBy([
                                "name" => MailTemplate::REFUSAL_PROJECT,
                                "callOfProject" => $validationForm->getData()->getCallOfProject()
                            ]);
                        }
                        $validationForm->add('mailTemplate', SummernoteType::class, [
                            'mapped' => false,
                            'data' => $mailTemplateData,
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

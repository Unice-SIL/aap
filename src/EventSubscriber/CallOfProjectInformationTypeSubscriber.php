<?php


namespace App\EventSubscriber;


use App\Entity\CallOfProject;
use App\Entity\ProjectFormLayout;
use App\Form\Type\InitProjectChoiceType;
use App\Manager\ProjectFormLayout\ProjectFormLayoutManagerInterface;
use App\Manager\ProjectFormWidget\ProjectFormWidgetManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Constraints\NotBlank;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class CallOfProjectInformationTypeSubscriber implements EventSubscriberInterface
{
    /**
     * @var ProjectFormWidgetManagerInterface
     */
    private $projectFormWidgetManager;
    /**
     * @var ProjectFormLayoutManagerInterface
     */
    private $projectFormLayoutManager;


    /**
     * CallOfProjectInformationTypeSubscriber constructor.
     * @param ProjectFormWidgetManagerInterface $projectFormWidgetManager
     * @param ProjectFormLayoutManagerInterface $projectFormLayoutManager
     */
    public function __construct(
        ProjectFormWidgetManagerInterface $projectFormWidgetManager,
        ProjectFormLayoutManagerInterface $projectFormLayoutManager
    )
    {
        $this->projectFormWidgetManager = $projectFormWidgetManager;
        $this->projectFormLayoutManager = $projectFormLayoutManager;
    }

    public static function getSubscribedEvents()
    {
        return [
                FormEvents::POST_SUBMIT => 'postSubmit',
                FormEvents::PRE_SET_DATA => 'preSetData',
        ];
    }

    public function postSubmit(FormEvent $event)
    {
        if ($event->getForm()->has('callOfProjectModel') or $event->getForm()->has('projectFormLayoutModel')) {

            if ($event->getForm()->has('callOfProjectModel')) {

                /** @var CallOfProject $callOfProjectModel */
                $callOfProjectModel = $event->getForm()->get('callOfProjectModel')->getData();

                if (!$callOfProjectModel instanceof CallOfProject) {
                    return;
                }

                $projectFormLayoutModel = $callOfProjectModel->getProjectFormLayout();
            }

            if ($event->getForm()->has('projectFormLayoutModel')) {

                /** @var ProjectFormLayout $projectFormLayoutModel */
                $projectFormLayoutModel = $event->getForm()->get('projectFormLayoutModel')->getData();

                if (!$projectFormLayoutModel instanceof ProjectFormLayout) {
                    return;
                }
            }

            /** @var CallOfProject $newCallOfProject */
            $newCallOfProject = $event->getData();

            $projectFormLayout = $this->projectFormLayoutManager->create($newCallOfProject);
            $projectFormWidgets = $projectFormLayoutModel->getProjectFormWidgets();

            foreach ($projectFormWidgets as $projectFormWidget) {
                $projectFormWidgetClone = $this->projectFormWidgetManager->cloneForNewProjectFormLayout($projectFormWidget);
                $projectFormLayout->addProjectFormWidget($projectFormWidgetClone);
            }
        }


    }

    public function preSetData(FormEvent $event)
    {
        /** @var CallOfProject $callOfProject */
        $callOfProject = $event->getData();

        $form = $event->getForm();

        if (null !== $callOfProject and null === $callOfProject->getId()) {

            // create builder for field
            $builder = $form
                ->getConfig()
                ->getFormFactory()
                ->createNamedBuilder('initProject', InitProjectChoiceType::class, null, array(
                'auto_initialize'=> false // it's important!!!
            ));

            // now you can add listener
            $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {

                $form = $event->getForm();
                $data = $form->getData();

                $formParent = $form->getParent();
                if ($data === InitProjectChoiceType::INIT_BY_CALL_OF_PROJECT) {

                    $formParent->add('callOfProjectModel', Select2EntityType::class, [
                        'multiple' => false,
                        'remote_route' => 'app.call_of_project.list_by_user_select_2',
                        'class' => CallOfProject::class,
                        'primary_key' => 'id',
                        'text_property' => 'name',
                        'minimum_input_length' => 2,
                        'page_limit' => 10,
                        'allow_clear' => true,
                        'delay' => 250,
                        'cache' => true,
                        'cache_timeout' => 60000, // if 'cache' is true
                        'placeholder' => 'app.call_of_project.select_2.placeholder',
                        'mapped' => false,
                        'label' => 'app.call_of_project.init.choices.init_by_call_of_project',
                        'required' => true,
                        'constraints' => [new NotBlank()]
                    ]);

                } elseif ($data === InitProjectChoiceType::INIT_BY_PROJECT_FORM_LAYOUT) {
                    $formParent->add('projectFormLayoutModel', Select2EntityType::class, [
                        'multiple' => false,
                        'remote_route' => 'app.project_form_layout.list_all_templates_select_2',
                        'class' => ProjectFormLayout::class,
                        'primary_key' => 'id',
                        'text_property' => 'name',
                        'minimum_input_length' => 0,
                        'page_limit' => false,
                        'allow_clear' => true,
                        'delay' => 250,
                        'cache' => true,
                        'cache_timeout' => 60000, // if 'cache' is true
                        'placeholder' => 'app.project_form_layout.select_2.placeholder',
                        'mapped' => false,
                        'label' => 'app.call_of_project.init.choices.init_by_project_form_layout',
                        'required' => true,
                        'constraints' => [new NotBlank()]
                    ]);
                }
            });

           // and only now you can add field to form
           $form->add($builder->getForm());

        }
    }
}
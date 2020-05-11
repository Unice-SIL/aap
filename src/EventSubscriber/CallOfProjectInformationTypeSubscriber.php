<?php


namespace App\EventSubscriber;


use App\Entity\CallOfProject;
use App\Manager\ProjectFormLayout\ProjectFormLayoutManagerInterface;
use App\Manager\ProjectFormWidget\ProjectFormWidgetManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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
                FormEvents::POST_SUBMIT => 'setProjectFormLayout',
                FormEvents::PRE_SET_DATA => 'preSetData',
        ];
    }

    public function setProjectFormLayout(FormEvent $event)
    {

        if (!$event->getForm()->has('callOfProjectModel')) {
            return;
        }

        /** @var CallOfProject $callOfProjectModel */
        $callOfProjectModel = $event->getForm()->get('callOfProjectModel')->getData();

        if (!$callOfProjectModel instanceof CallOfProject) {
            return;
        }

        /** @var CallOfProject $newCallOfProject */
        $newCallOfProject = $event->getData();

        $projectFormWidgets = $callOfProjectModel->getProjectFormLayout()->getProjectFormWidgets();

        $projectFormLayout = $this->projectFormLayoutManager->create($newCallOfProject);

        foreach ($projectFormWidgets as $projectFormWidget) {
            $projectFormWidgetClone = $this->projectFormWidgetManager->cloneForNEwProjectFormLayout($projectFormWidget);
            $projectFormLayout->addProjectFormWidget($projectFormWidgetClone);
        }

    }

    public function preSetData(FormEvent $event)
    {
        /** @var CallOfProject $callOfProject */
        $callOfProject = $event->getData();

        $form = $event->getForm();

        if (null !== $callOfProject and null === $callOfProject->getId()) {

            $form->add('callOfProjectModel', Select2EntityType::class, [
                'multiple' => false,
                'remote_route' => 'app.call_of_project.list_by_user_select_2',
                //'remote_params' => [], // static route parameters for request->query
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
                'label' => 'app.call_of_project.init_by_call_of_projects'
            ]);
        }
    }
}
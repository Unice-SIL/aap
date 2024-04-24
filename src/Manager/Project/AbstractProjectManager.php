<?php


namespace App\Manager\Project;


use App\Entity\CallOfProject;
use App\Entity\Project;
use App\Event\RefuseProjectEvent;
use App\Event\ValidateProjectEvent;
use App\Exception\BadProjectTransitionException;
use App\Exception\ProjectTransitionException;
use App\Manager\Notification\NotificationManagerInterface;
use App\Manager\ProjectContent\ProjectContentManagerInterface;
use App\Utils\Mail\MailHelper;
use App\Widget\FormWidget\FormWidgetInterface;
use Exception;
use LogicException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Workflow\Registry;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractProjectManager implements ProjectManagerInterface
{
    /**
     * @var ProjectContentManagerInterface
     */
    protected $projectContentManager;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var NotificationManagerInterface
     */
    protected $notificationManager;

    /**
     * @var MailHelper
     */
    protected $mailHelper;

    /**
     * @var Registry
     */
    protected $workflowRegistry;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param ProjectContentManagerInterface $projectContentManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param NotificationManagerInterface $notificationManager
     * @param MailHelper $mailHelper
     * @param Registry $workflowRegistry
     * @param TranslatorInterface $translator
     */
    public function __construct(
        ProjectContentManagerInterface $projectContentManager,
        EventDispatcherInterface $eventDispatcher,
        NotificationManagerInterface $notificationManager,
        MailHelper $mailHelper,
        Registry $workflowRegistry,
        TranslatorInterface $translator
    ) {
        $this->projectContentManager = $projectContentManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->notificationManager = $notificationManager;
        $this->mailHelper = $mailHelper;
        $this->workflowRegistry = $workflowRegistry;
        $this->translator = $translator;
    }

    /**
     * @throws Exception
     */
    public function create(CallOfProject $callOfProject): Project
    {
        $project = new Project();
        $project->setCallOfProject($callOfProject);
        $this->refreshProjectContents($project);

        return $project;
    }

    /**
     * Add a ProjectContent for every ProjectFormWidget if doesn't exists
     * @param Project $project
     * @throws Exception
     */
    public function refreshProjectContents(Project $project): void
    {
        $projectFormWidgets = $project->getCallOfProject()->getProjectFormLayout()->getProjectFormWidgets();
        $projectContents = $project->getProjectContents();

        foreach ($projectFormWidgets as $projectFormWidget) {

            if (!$projectFormWidget->getWidget() instanceof FormWidgetInterface) {
                continue;
            }
            $projectContentsFiltered = $projectContents->filter(function ($projectContent) use ($projectFormWidget) {
                return $projectContent->getProjectFormWidget() === $projectFormWidget;
            });

            if ($projectContentsFiltered->isEmpty()) {
                $projectContent = $this->projectContentManager->create($projectFormWidget);
                $project->addProjectContent($projectContent);
            }

        }

    }

    /**
     * @param Project $project
     * @param string $transition
     * @return void
     * @throws ProjectTransitionException
     */
    public function validateOrRefuse(Project $project, string $transition)
    {
        if (!in_array($transition, [Project::TRANSITION_VALIDATE, Project::TRANSITION_REFUSE])) {
            throw new ProjectTransitionException('app.flash_message.error_project_transition');
        }

        try {
            $stateMachine = $this->workflowRegistry->get($project, 'project_validation_process');
            $stateMachine->apply($project, $transition);

            $event = $project->getStatus() === Project::STATUS_VALIDATED ? new ValidateProjectEvent($project) : new RefuseProjectEvent($project);
            $this->eventDispatcher->dispatch($event, $event->getEventName());

            $this->update($project);
        } catch (LogicException $exception) {
            throw new ProjectTransitionException(
               $this->translator->trans('app.flash_message.error_project_' . $transition, ['%item%' => $project->getName()])
            );
        }
    }

    public abstract function save(Project $project);

    public abstract function update(Project $project);

    public abstract function delete(Project $project);


}
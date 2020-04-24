<?php


namespace App\Manager\Project;


use App\Entity\CallOfProject;
use App\Entity\Project;
use App\Manager\ProjectContent\ProjectContentManagerInterface;
use App\Widget\FormWidget\FormWidgetInterface;

abstract class AbstractProjectManager implements ProjectManagerInterface
{
    /**
     * @var ProjectContentManagerInterface
     */
    private $projectContentManager;

    /**
     * AbstractProjectManager constructor.
     * @param ProjectContentManagerInterface $projectContentManager
     */
    public function __construct(ProjectContentManagerInterface $projectContentManager)
    {
        $this->projectContentManager = $projectContentManager;
    }

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
     */
    public function refreshProjectContents(Project $project)
    {
        $projectFormWidgets = $project->getCallOfProject()->getProjectFormLayout()->getProjectFormWidgets();
        $projectContents = $project->getProjectContents();

        foreach ($projectFormWidgets as $projectFormWidget) {

            if (!$projectFormWidget->getWidget() instanceof FormWidgetInterface) {
                continue;
            }

            $projectContents = $projectContents->filter(function ($projectContent) use ($projectFormWidget) {
                return $projectContent->getProjectFormWidget();
            });

            if ($projectContents->isEmpty()) {
                $projectContent = $this->projectContentManager->create($projectFormWidget);
                $project->addProjectContent($projectContent);
            }
        }

    }

    public abstract function save(Project $project);

    public abstract function update(Project $project);

    public abstract function delete(Project $project);


}
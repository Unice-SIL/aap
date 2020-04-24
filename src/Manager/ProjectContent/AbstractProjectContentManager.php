<?php


namespace App\Manager\ProjectContent;


use App\Entity\ProjectContent;
use App\Entity\ProjectFormWidget;

abstract class AbstractProjectContentManager implements ProjectContentManagerInterface
{

    public function create(ProjectFormWidget $projectFormWidget): ProjectContent
    {
        $projectFormContent = new ProjectContent();
        $projectFormContent->setProjectFormWidget($projectFormWidget);

        return $projectFormContent;
    }

    public abstract function save(ProjectContent $projectContent);

    public abstract function update(ProjectContent $projectContent);

    public abstract function delete(ProjectContent $projectContent);


}
<?php


namespace App\Event;


use App\Entity\Project;
use Symfony\Contracts\EventDispatcher\Event;

class RefuseProjectEvent extends Event
{
    const NAME = 'refuse_project';
    /**
     * @var Project
     */
    private $project;

    /**
     * AddProjectEvent constructor.
     * @param Project $project
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    /**
     * @return Project
     */
    public function getProject(): Project
    {
        return $this->project;
    }

    public function getEventName()
    {
        return self::NAME;
    }
}
<?php

namespace App\Manager\Project;

use App\Entity\Project;
use App\Event\AddProjectEvent;
use App\Manager\ProjectContent\ProjectContentManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DoctrineProjectManager extends AbstractProjectManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * DoctrineProjectManager constructor.
     * @param EntityManagerInterface $em
     * @param ProjectContentManagerInterface $projectContentManager
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EntityManagerInterface $em, ProjectContentManagerInterface $projectContentManager, EventDispatcherInterface $dispatcher)
    {
        parent::__construct($projectContentManager);
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    public function save(Project $project)
    {
        $this->em->persist($project);
        $this->em->flush();

        $event = new AddProjectEvent($project);
        $this->dispatcher->dispatch($event, AddProjectEvent::NAME);
    }

    public function update(Project $project)
    {
        $this->em->flush();
    }

    public function delete(Project $project)
    {
        $this->em->remove($project);
        $this->em->flush();
    }

}
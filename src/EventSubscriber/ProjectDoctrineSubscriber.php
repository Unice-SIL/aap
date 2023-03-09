<?php

namespace App\EventSubscriber;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class ProjectDoctrineSubscriber implements EventSubscriber
{
    /**
     * @var  ProjectRepository
     */
    private $projectRepository;

    /**
     * @param ProjectRepository $projectRepository
     */
    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist
        ];
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        /** @var Project $project */
        $project = $args->getObject();

        if (!$project instanceof Project) {
            return;
        }

        $project->setNumber($this->projectRepository->getMaxNumber($project->getCallOfProject()) + 1);
    }
}

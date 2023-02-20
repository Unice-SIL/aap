<?php

namespace App\EventSubscriber;

use App\Constant\MailTemplate;
use App\Entity\CallOfProject;
use App\Entity\ProjectFormLayout;
use App\Manager\ProjectFormLayout\ProjectFormLayoutManagerInterface;
use App\Repository\CallOfProjectRepository;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class CallOfProjectSubscriber implements EventSubscriber
{
    /**
     * @var CallOfProjectRepository
     */
    private $callOfProjectRepository;

    // this method can only return the event names; you cannot define a
    // custom method name to execute when each event triggers
    /**
     * @var ProjectFormLayoutManagerInterface
     */
    private $projectFormLayoutManager;

    /**
     * CallOfProjectSubscriber constructor.
     * @param ProjectFormLayoutManagerInterface $projectFormLayoutManager
     */
    public function __construct(ProjectFormLayoutManagerInterface $projectFormLayoutManager, CallOfProjectRepository  $callOfProjectRepository)
    {
        $this->projectFormLayoutManager = $projectFormLayoutManager;
        $this->callOfProjectRepository = $callOfProjectRepository;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
        ];
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        /** @var CallOfProject $callOfProject */
        $callOfProject = $args->getObject();

        if (!$callOfProject instanceof CallOfProject) {
            return;
        }

        if ($callOfProject->getProjectFormLayouts()->count() == 0) {
            $this->projectFormLayoutManager->create($callOfProject);;
        }

        $lastCode = 1 + $this->callOfProjectRepository->getLastCode();
        $callOfProject->setCode($lastCode);
    }

}

<?php

namespace App\EventSubscriber;

use App\Constant\MailTemplate;
use App\Entity\CallOfProject;
use App\Entity\ProjectFormLayout;
use App\Manager\ProjectFormLayout\ProjectFormLayoutManagerInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class CallOfProjectSubscriber implements EventSubscriber
{


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
    public function __construct(ProjectFormLayoutManagerInterface $projectFormLayoutManager)
    {
        $this->projectFormLayoutManager = $projectFormLayoutManager;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
        ];
    }

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
    }

}

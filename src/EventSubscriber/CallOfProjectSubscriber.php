<?php

namespace App\EventSubscriber;

use App\Entity\CallOfProject;
use App\Entity\ProjectFormLayout;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class CallOfProjectSubscriber implements EventSubscriber
{
    // this method can only return the event names; you cannot define a
    // custom method name to execute when each event triggers
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
            $projectFormLayout = new ProjectFormLayout();
            $projectFormLayout->setName($callOfProject->getName() . ' ' . 'formulaire');
            $callOfProject->addProjectFormLayout($projectFormLayout);
        }

    }

}

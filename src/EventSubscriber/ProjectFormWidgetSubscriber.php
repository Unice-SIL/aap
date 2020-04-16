<?php

namespace App\EventSubscriber;

use App\Entity\ProjectFormWidget;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;


class ProjectFormWidgetSubscriber implements EventSubscriber
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
        /** @var ProjectFormWidget $projectFormWidget */
        $projectFormWidget = $args->getObject();

        if (!$projectFormWidget instanceof ProjectFormWidget) {
            return;
        }

        if (!$projectFormWidget->getPosition()) {

            $projectFormWidgets = $projectFormWidget->getProjectFormLayout()->getProjectFormWidgets();

            $positions = $projectFormWidgets->map(function($projectFormWidget) {
                return $projectFormWidget->getPosition();
            })->toArray();

            $position = max($positions);

            $projectFormWidget->setPosition(++$position);
        }
    }

}

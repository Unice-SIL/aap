<?php

namespace App\EventSubscriber;

use App\Entity\ProjectFormWidget;
use App\Widget\FormWidget\FormWidgetInterface;
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
            Events::preUpdate,
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

            $projectFormWidgets = $projectFormWidget->getProjectFormLayout()->getAllProjectFormWidgets();

            $positions = $projectFormWidgets->map(function($projectFormWidget) {
                return $projectFormWidget->getPosition();
            })->toArray();

            $position = max($positions);

            $projectFormWidget->setPosition(++$position);
        }

        $this->setWidgetClass($projectFormWidget);
        $this->setTitle($projectFormWidget);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        /** @var ProjectFormWidget $projectFormWidget */
        $projectFormWidget = $args->getObject();

        if (!$projectFormWidget instanceof ProjectFormWidget) {
            return;
        }

        $this->setWidgetClass($projectFormWidget);
        $this->setTitle($projectFormWidget);
    }

    private function setWidgetClass(ProjectFormWidget $projectFormWidget)
    {
        if ($widget = $projectFormWidget->getWidget()) {
            $projectFormWidget->setWidgetClass(get_class($widget));
        }
    }

    private function setTitle(ProjectFormWidget $projectFormWidget)
    {
        if ($widget = $projectFormWidget->getWidget() and $widget instanceof FormWidgetInterface) {
            $projectFormWidget->setTitle($widget->getLabel());
        }
    }
}

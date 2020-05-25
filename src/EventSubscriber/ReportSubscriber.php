<?php

namespace App\EventSubscriber;

use App\Entity\Report;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class ReportSubscriber implements EventSubscriber
{

    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        /** @var Report $report */
        $report = $args->getObject();

        if (!$report instanceof Report) {
            return;
        }

        if (null === $report->getName()) {
            $report->setName('Rapport de ' . $report->getReporter() . ' sur le projet ' . $report->getStatus());
        }

    }

}

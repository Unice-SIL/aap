<?php

namespace App\EventSubscriber;

use App\Entity\Report;
use App\Utils\Mail\MailHelper;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class ReportSubscriber implements EventSubscriber
{
    /**
     * @var MailHelper
     */
    private $mailHelper;

    /**
     * ReportSubscriber constructor.
     * @param MailHelper $mailHelper
     */
    public function __construct(MailHelper $mailHelper)
    {
        $this->mailHelper = $mailHelper;
    }

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
            $report->setName('Rapport de ' . $report->getReporter() . ' sur le projet ' . $report->getProject()->getName());
        }

        if ($report->getNotifyReporters() === Report::NOTIFY_REPORT) {
            $this->mailHelper->notifyReporterAboutReport($report);
        }

        if ($report->getNotifyReporters() === Report::NOTIFY_REPORTS) {
            $this->mailHelper->notifyReporterAboutReports($report);
        }

    }

}

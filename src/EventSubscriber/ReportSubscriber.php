<?php

namespace App\EventSubscriber;

use App\Entity\Report;
use App\Utils\Mail\MailHelper;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

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

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::postUpdate,
        ];
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $report = $args->getObject();

        if (!$report instanceof Report) {
            return;
        }

        if (null === $report->getName()) {
            $report->setName('Rapport de ' . $report->getReporter() . ' sur le projet ' . $report->getProject()->getName());
        }

        if ($report->getNotifyReporters() === Report::NOTIFY_REPORT) {
            $this->mailHelper->notificationUserNewReporter($report);
        }

        if ($report->getNotifyReporters() === Report::NOTIFY_REPORTS) {
            $this->mailHelper->notificationUserNewReporters($report);
        }

    }

    /**
     * @param LifecycleEventArgs $args
     * @return void
     * @throws TransportExceptionInterface
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $report = $args->getObject();

        if (!$report instanceof Report) {
            return;
        }

        $this->mailHelper->notificationReporterReportUpdated($report);
        $this->mailHelper->notificationCopFollowersReportUpdated($report);
    }

}

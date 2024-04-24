<?php


namespace App\EventSubscriber;


use App\Event\AddProjectEvent;
use App\Event\RefuseProjectEvent;
use App\Event\ValidateProjectEvent;
use App\Utils\Mail\MailHelper;
use App\Utils\Notification\NotificationHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class ProjectSubscriber implements EventSubscriberInterface
{
    /**
     * @var MailHelper
     */
    private $mailHelper;

    /**
     * @var NotificationHelper
     */
    private $notificationHelper;


    /**
     * @param MailHelper $mailHelper
     * @param NotificationHelper $notificationHelper
     */
    public function __construct(MailHelper $mailHelper, NotificationHelper $notificationHelper)
    {
        $this->mailHelper = $mailHelper;
        $this->notificationHelper = $notificationHelper;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AddProjectEvent::NAME => 'onAddProject',
            ValidateProjectEvent::NAME => 'onValidateProject',
            RefuseProjectEvent::NAME => 'onRefuseProject'
        ];
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function onAddProject(AddProjectEvent $event)
    {
        $project = $event->getProject();

        $this->mailHelper->notificationUserNewProject($project);
        $this->mailHelper->notificationCopFollowersNewProject($project);
    }

    /**
     * @param ValidateProjectEvent $event
     * @return void
     * @throws TransportExceptionInterface
     */
    public function onValidateProject(ValidateProjectEvent $event)
    {
        $project = $event->getProject();
        $this->notificationHelper->notificationUserValidationProject($project);
        $this->mailHelper->notificationUserValidationProject($project);
    }

    /**
     * @param RefuseProjectEvent $event
     * @return void
     * @throws TransportExceptionInterface
     */
    public function onRefuseProject(RefuseProjectEvent $event)
    {
        $project = $event->getProject();
        $this->notificationHelper->notificationUserRefusalProject($project);
        $this->mailHelper->notificationUserRefusalProject($project);
    }
}
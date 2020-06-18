<?php


namespace App\EventSubscriber;


use App\Event\AddProjectEvent;
use App\Utils\Mail\MailHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProjectSubscriber implements EventSubscriberInterface
{
    /**
     * @var MailHelper
     */
    private $mailHelper;


    /**
     * ProjectSubscriber constructor.
     * @param MailHelper $mailHelper
     */
    public function __construct(MailHelper $mailHelper)
    {
        $this->mailHelper = $mailHelper;
    }

    public static function getSubscribedEvents()
    {
        return [
              AddProjectEvent::NAME => 'onAddProject'
        ];
    }

    public function onAddProject(AddProjectEvent $event)
    {
        $project = $event->getProject();

        $this->mailHelper->notifyCreatorOfANewProject($project);
    }
}
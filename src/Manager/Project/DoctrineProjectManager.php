<?php

namespace App\Manager\Project;

use App\Entity\Project;
use App\Event\AddProjectEvent;
use App\Manager\Notification\NotificationManagerInterface;
use App\Manager\ProjectContent\ProjectContentManagerInterface;
use App\Utils\Mail\MailHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Workflow\Registry;
use Symfony\Contracts\Translation\TranslatorInterface;

class DoctrineProjectManager extends AbstractProjectManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param ProjectContentManagerInterface $projectContentManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param NotificationManagerInterface $notificationManager
     * @param MailHelper $mailHelper
     * @param Registry $workflowRegistry
     * @param TranslatorInterface $translator
     * @param EntityManagerInterface $em
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(
        ProjectContentManagerInterface $projectContentManager,
        EventDispatcherInterface $eventDispatcher,
        NotificationManagerInterface $notificationManager,
        MailHelper $mailHelper,
        Registry $workflowRegistry,
        TranslatorInterface $translator,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher
    )
    {
        parent::__construct(
            $projectContentManager,
            $eventDispatcher,
            $notificationManager,
            $mailHelper,
            $workflowRegistry,
            $translator
        );
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param Project $project
     * @return void
     */
    public function save(Project $project)
    {
        $this->em->persist($project);
        $this->em->flush();

        $event = new AddProjectEvent($project);
        $this->dispatcher->dispatch($event, AddProjectEvent::NAME);
    }

    /**
     * @param Project $project
     * @return void
     */
    public function update(Project $project)
    {
        $this->em->flush();
    }

    /**
     * @param Project $project
     * @return void
     */
    public function delete(Project $project)
    {
        $this->em->remove($project);
        $this->em->flush();
    }

}
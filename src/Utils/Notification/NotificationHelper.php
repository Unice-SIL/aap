<?php

namespace App\Utils\Notification;

use App\Entity\Project;
use App\Manager\Notification\NotificationManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class NotificationHelper
{

    /**
     * @var NotificationManagerInterface
     */
    private $notificationManager;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param NotificationManagerInterface $notificationManager
     * @param TranslatorInterface $translator
     */
    public function __construct(NotificationManagerInterface $notificationManager, TranslatorInterface $translator)
    {
        $this->notificationManager = $notificationManager;
        $this->translator = $translator;
    }

    /**
     * @param Project $project
     * @return void
     */
    private function notificationUserValidateRefuseProject(Project $project)
    {
        $notification = $this->notificationManager->create();
        $notificationTitle = $project->getStatus() === Project::STATUS_VALIDATED ? 'app.notifications.project_validated' : 'app.notifications.project_refused';
        $notification->setTitle($this->translator->trans($notificationTitle, ['%project%' => $project->getName()]));
        $notification->setRouteName('app.project.show');
        $notification->setRouteParams(['id' => $project->getId()]);
        $project->getCreatedBy()->addNotification($notification);
    }

    /**
     * @param Project $project
     * @return void
     */
    public function notificationUserValidationProject(Project $project)
    {
        $this->notificationUserValidateRefuseProject($project);
    }

    /**
     * @param Project $project
     * @return void
     */
    public function notificationUserRefusalProject(Project $project)
    {
        $this->notificationUserValidateRefuseProject($project);
    }
}
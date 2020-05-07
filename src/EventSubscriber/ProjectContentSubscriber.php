<?php

namespace App\EventSubscriber;

use App\Entity\ProjectContent;
use App\Utils\File\FileUploaderInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;


class ProjectContentSubscriber implements EventSubscriber
{

    /**
     * @var FileUploaderInterface
     */
    private $fileUploader;

    public function __construct(FileUploaderInterface $fileUploader)
    {
        $this->fileUploader = $fileUploader;
    }

    // this method can only return the event names; you cannot define a
    // custom method name to execute when each event triggers
    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate,
            Events::postLoad,
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        /** @var ProjectContent $projectContent */
        $projectContent = $args->getObject();

        if (!$projectContent instanceof ProjectContent) {
            return;
        }

        $this->reverseTransform($projectContent);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        /** @var ProjectContent $projectContent */
        $projectContent = $args->getObject();

        if (!$projectContent instanceof ProjectContent) {
            return;
        }

        $this->reverseTransform($projectContent);
    }

    private function reverseTransform(ProjectContent $projectContent)
    {
        $widget = $projectContent->getProjectFormWidget()->getWidget();
        $content = $projectContent->getContent();

        $content = $widget->reverseTransformData($content, ['file_uploader' => $this->fileUploader]);

        $projectContent->setContent($content);
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        /** @var ProjectContent $projectContent */
        $projectContent = $args->getObject();

        if (!$projectContent instanceof ProjectContent) {
            return;
        }

        $widget = $projectContent->getProjectFormWidget()->getWidget();
        $content = $projectContent->getContent();

        $content = $widget->transformData($content, ['file_uploader' => $this->fileUploader]);

        $projectContent->setContent($content);

    }

}

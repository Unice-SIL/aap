<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CallOfProjectMailTemplateRepository;

/**
 * @ORM\Entity(repositoryClass=CallOfProjectMailTemplateRepository::class)
 */
class CallOfProjectMailTemplate extends AbstractMailTemplate
{
    public const ALLOWED_TEMPLATES = [
        \App\Constant\MailTemplate::NOTIFICATION_USER_NEW_PROJECT,
        \App\Constant\MailTemplate::NOTIFICATION_USER_NEW_REPORTER,
        \App\Constant\MailTemplate::NOTIFICATION_USER_NEW_REPORTERS,
        \App\Constant\MailTemplate::NOTIFICATION_COP_FOLLOWERS_NEW_PROJECT,
        \App\Constant\MailTemplate::NOTIFICATION_USER_VALIDATION_PROJECT,
        \App\Constant\MailTemplate::NOTIFICATION_USER_REFUSAL_PROJECT,
        \App\Constant\MailTemplate::NOTIFICATION_REPORTER_REPORT_UPDATED,
        \App\Constant\MailTemplate::NOTIFICATION_COP_FOLLOWERS_REPORT_UPDATED
    ];

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CallOfProject", inversedBy="mailTemplates")
     * @ORM\JoinColumn(nullable=false)
     */
    private $callOfProject = null;

    /**
     * @return null
     */
    public function getCallOfProject()
    {
        return $this->callOfProject;
    }

    /**
     * @param null $callOfProject
     */
    public function setCallOfProject($callOfProject): self
    {
        $this->callOfProject = $callOfProject;
        return $this;
    }

    public function initFromMailTemplate(MailTemplate $mailTemplate): CallOfProjectMailTemplate
    {
        $this
            ->setName($mailTemplate->getName())
            ->setSubject($mailTemplate->getSubject())
            ->setBody($mailTemplate->getBody());

        return $this;
    }
}

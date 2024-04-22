<?php


namespace App\Utils\Mail;


use App\Entity\CallOfProject;
use App\Entity\CallOfProjectMailTemplate;
use App\Entity\Invitation;
use App\Entity\MailTemplate;
use App\Entity\Project;
use App\Entity\Report;
use App\Entity\User;
use App\Repository\CallOfProjectMailTemplateRepository;
use App\Repository\MailTemplateRepository;
use Exception;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class MailHelper
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var string
     */
    private $mailFrom;
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;
    /**
     * @var MailTemplateRepository
     */
    private $mailTemplateRepository;

    /**
     * @var CallOfProjectMailTemplateRepository
     */
    private $callOfProjectMailTemplateRepository;


    /**
     * MailHelper constructor.
     * @param \Swift_Mailer $mailer
     * @param string $mailFrom
     * @param UrlGeneratorInterface $urlGenerator
     * @param MailTemplateRepository $mailTemplateRepository
     * @param CallOfProjectMailTemplateRepository $callOfProjectMailTemplateRepository
     */
    public function __construct(
        \Swift_Mailer $mailer,
        string $mailFrom,
        UrlGeneratorInterface $urlGenerator,
        MailTemplateRepository $mailTemplateRepository,
        CallOfProjectMailTemplateRepository $callOfProjectMailTemplateRepository
    )
    {
        $this->mailer = $mailer;
        $this->mailFrom = $mailFrom;
        $this->urlGenerator = $urlGenerator;
        $this->mailTemplateRepository = $mailTemplateRepository;
        $this->callOfProjectMailTemplateRepository = $callOfProjectMailTemplateRepository;
    }

    /**
     * @param string $name
     * @param CallOfProject $callOfProject
     * @return CallOfProjectMailTemplate|null
     */
    private function getEmailTemplateFromCallOfProject(string $name, CallOfProject $callOfProject): ?MailTemplate
    {
        $mailTemplate = $this->callOfProjectMailTemplateRepository->findOneBy([
            'callOfProject' => $callOfProject->getId(),
            'name' => $name
        ]);

        if (!$mailTemplate instanceof CallOfProjectMailTemplate) {
            $mailTemplate = $this->getGenericEmailTemplate($name);
        }

        return $mailTemplate;
    }

    /**
     * @param string $name
     * @return MailTemplate|null
     */
    private function getGenericEmailTemplate(string $name): ?MailTemplate
    {
        return $mailTemplate = $this->mailTemplateRepository->findOneByName($name);
    }

    /**
     * @param string $message
     * @param Project $project
     * @return string|string[]
     */
    public static function parseMessageWithProject(string $message, Project $project)
    {
        $owner = $project->getCreatedBy();
        $message = str_replace(\App\Constant\MailTemplate::PLACEHOLDER_FIRSTNAME, $owner->getFirstname(), $message);
        $message = str_replace(\App\Constant\MailTemplate::PLACEHOLDER_LASTNAME, $owner->getLastname(), $message);
        $message = str_replace(\App\Constant\MailTemplate::PLACEHOLDER_CALL_OF_PROJECT_NAME, $project->getCallOfProject()->getName(), $message);
        $message = str_replace(\App\Constant\MailTemplate::PLACEHOLDER_PROJECT_NAME, $project->getName(), $message);

        return $message;
    }

    /**
     * @param Report $report     *
     */
    public function notificationUserNewReporter(Report $report)
    {
        $callOfProject = $report->getProject()->getCallOfProject();
        $mailTemplate = $this->getEmailTemplateFromCallOfProject(\App\Constant\MailTemplate::NOTIFICATION_USER_NEW_REPORTER, $callOfProject);

        if (!$mailTemplate instanceof MailTemplate) return;
        if (!$mailTemplate->isEnable()) return;

        $message = new \Swift_Message(
            $mailTemplate->getSubject(),
            self::parseMessageWithProject($mailTemplate->getBody(), $report->getProject())
        );
        $message
            ->setFrom($this->mailFrom)
            ->setTo($report->getReporter()->getEmail())
            ->setContentType('text/html')
        ;

        $this->mailer->send($message);
    }

    /**
     * @param Report $report
     */
    public function notificationUserNewReporters(Report $report)
    {
        static $reportersNotified = [];
        if (in_array($report->getReporter(), $reportersNotified)) {
            return;
        }
        $reportersNotified[] = $report->getReporter();

        $callOfProject = $report->getProject()->getCallOfProject();
        $mailTemplate = $this->getEmailTemplateFromCallOfProject(\App\Constant\MailTemplate::NOTIFICATION_USER_NEW_REPORTERS, $callOfProject);

        if (!$mailTemplate instanceof MailTemplate) return;
        if (!$mailTemplate->isEnable()) return;

        $message = new \Swift_Message(
            $mailTemplate->getSubject(),
            self::parseMessageWithProject($mailTemplate->getBody(), $report->getProject())
        );
        $message
            ->setFrom($this->mailFrom)
            ->setTo($report->getReporter()->getEmail())
            ->setContentType('text/html')
        ;

        $this->mailer->send($message);
    }

    /**
     * @param Invitation $invitation
     * @throws Exception
     */
    public function notificationUserInvitation(Invitation $invitation)
    {
        if ($invitation->getToken() === null) {
            throw new Exception('The token is null');
        }
        $url = $this->urlGenerator->generate('app.process_after_shibboleth_connection', ['token' => $invitation->getToken()], UrlGeneratorInterface::ABSOLUTE_URL);
        $user = $invitation->getUser();

        $mailTemplate = $this->getGenericEmailTemplate(\App\Constant\MailTemplate::NOTIFICATION_USER_INVITATION);

        if (!$mailTemplate instanceof MailTemplate) return;
        if (!$mailTemplate->isEnable()) return;

        $message = str_replace(\App\Constant\MailTemplate::PLACEHOLDER_FIRSTNAME, $user->getFirstname(), $mailTemplate->getBody());
        $message = str_replace(\App\Constant\MailTemplate::PLACEHOLDER_LASTNAME, $user->getLastname(), $message);
        $message = str_replace(\App\Constant\MailTemplate::PLACEHOLDER_URL_INVITATION, $url, $message);
        $message = new \Swift_Message(
            $mailTemplate->getSubject(),
            $message
        );
        $message
            ->setFrom($this->mailFrom)
            ->setTo($invitation->getUser()->getEmail())
            ->setContentType('text/html')
        ;

        $this->mailer->send($message);

        $invitation->setSentAt(new \DateTime());
    }

    /**
     * @param Project $project
     */
    public function notificationUserNewProject(Project $project)
    {
        $callOfProject = $project->getCallOfProject();
        $mailTemplate = $this->getEmailTemplateFromCallOfProject(\App\Constant\MailTemplate::NOTIFICATION_USER_NEW_PROJECT, $callOfProject);

        if (!$mailTemplate instanceof MailTemplate) return;
        if (!$mailTemplate->isEnable()) return;

        $message = new \Swift_Message(
            $mailTemplate->getSubject(),
            self::parseMessageWithProject($mailTemplate->getBody(), $project)
        );
        $message
            ->setFrom($this->mailFrom)
            ->setTo($project->getCreatedBy()->getEmail())
            ->setContentType('text/html')
        ;

        $this->mailer->send($message);
    }

    /**
     * @param Project $project
     */
    public function notificationCopFollowersNewProject(Project $project)
    {
        $callOfProject = $project->getCallOfProject();
        $mailTemplate = $this->getEmailTemplateFromCallOfProject(\App\Constant\MailTemplate::NOTIFICATION_COP_FOLLOWERS_NEW_PROJECT, $callOfProject);

        if (!$mailTemplate instanceof MailTemplate) return;
        if (!$mailTemplate->isEnable()) return;

        $mails = $project->getCallOfProject()->getSubscribers()->map(function (User $subscriber)
        {
           return $subscriber->getEmail();
        });

        $message = new \Swift_Message(
            $mailTemplate->getSubject(),
            self::parseMessageWithProject($mailTemplate->getBody(), $project)
        );
        $message
            ->setFrom($this->mailFrom)
            ->setTo($mails->toArray())
            ->setContentType('text/html')
        ;

        $this->mailer->send($message);
    }
}
<?php


namespace App\Utils\Mail;


use App\Entity\CallOfProject;
use App\Entity\CallOfProjectMailTemplate;
use App\Entity\Interfaces\MailTemplateInterface;
use App\Entity\Invitation;
use App\Entity\MailTemplate;
use App\Entity\Project;
use App\Entity\Report;
use App\Entity\User;
use App\Repository\CallOfProjectMailTemplateRepository;
use App\Repository\MailTemplateRepository;
use Exception;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class MailHelper
{
    /**
     * @var MailerInterface
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
     * @param string $mailFrom
     * @param MailerInterface $mailer
     * @param UrlGeneratorInterface $urlGenerator
     * @param MailTemplateRepository $mailTemplateRepository
     * @param CallOfProjectMailTemplateRepository $callOfProjectMailTemplateRepository
     */
    public function __construct(
        string $mailFrom,
        MailerInterface $mailer,
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
     * @return MailTemplateInterface|null
     */
    private function getEmailTemplateFromCallOfProject(string $name, CallOfProject $callOfProject): ?MailTemplateInterface
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
        return $this->mailTemplateRepository->findOneByName($name);
    }

    /**
     * @param string $templateName
     * @param $entity
     * @param User $recipient
     * @return void
     * @throws TransportExceptionInterface
     */
    private function sendMail(string $templateName, $entity, User $recipient)
    {
        $callOfProject = null;

        if ($entity instanceof CallOfProject) {
            $callOfProject = $entity;
        } elseif ($entity instanceof Project) {
            $callOfProject = $entity->getCallOfProject();
        } elseif ($entity instanceof Report) {
            $callOfProject = $entity->getProject()->getCallOfProject();
        }

        $mailTemplate = null;
        if ($callOfProject instanceof CallOfProject) {
            $mailTemplate = $this->getEmailTemplateFromCallOfProject($templateName, $callOfProject);
        }

        if (!$mailTemplate instanceof MailTemplate or !$mailTemplate->isEnable()) return;
        if (empty($recipient->getEmail())) return;

        $email = (new Email())
            ->from($this->mailFrom)
            ->to($recipient->getEmail())
            ->subject($mailTemplate->getSubject())
            ->html($this->parseMessageWithProject($mailTemplate->getBody(), $entity, $recipient));

        $this->mailer->send($email);
    }

    /**
     * @param string $message
     * @param $entity
     * @param User $recipient
     * @return array|string|string[]
     */
    public function parseMessageWithProject(string $message, $entity, User $recipient)
    {
        $callOfProject = null;
        $project = null;
        $report = null;
        $invitation = null;

        if ($entity instanceof Invitation) {
            $invitation = $entity;
        }

        if ($entity instanceof Report) {
            $report = $entity;
            $project = $report->getProject();
            $callOfProject = $project->getCallOfProject();
        }

        if ($entity instanceof Project) {
            $project = $entity;
            $callOfProject = $project->getCallOfProject();
        }

        $message = str_replace(\App\Constant\MailTemplate::PLACEHOLDER_FIRSTNAME, $recipient->getFirstname(), $message);
        $message = str_replace(\App\Constant\MailTemplate::PLACEHOLDER_LASTNAME, $recipient->getLastname(), $message);

        if ($callOfProject instanceof CallOfProject) {
            $message = str_replace(\App\Constant\MailTemplate::PLACEHOLDER_CALL_OF_PROJECT_NAME, $callOfProject->getName(), $message);
            $message = str_replace(\App\Constant\MailTemplate::PLACEHOLDER_CALL_OF_PROJECT_START_DATE, $callOfProject->getStartDate()->format('d/m/Y H:i'), $message);
            $message = str_replace(\App\Constant\MailTemplate::PLACEHOLDER_CALL_OF_PROJECT_END_DATE, $callOfProject->getEndDate()->format('d/m/Y H:i'), $message);
            $message = str_replace(\App\Constant\MailTemplate::PLACEHOLDER_CALL_OF_PROJECT_MANAGER_LINK,
                $this->urlGenerator->generate(
                    'app.call_of_project.informations',
                    ['id' => $callOfProject->getId()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
                $message
            );
        }

        if ($project instanceof Project) {
            $message = str_replace(\App\Constant\MailTemplate::PLACEHOLDER_PROJECT_NAME, $project->getName(), $message);
            $message = str_replace(\App\Constant\MailTemplate::PLACEHOLDER_PROJECT_CREATOR_LASTNAME, $project->getCreatedBy()->getLastname(), $message);
            $message = str_replace(\App\Constant\MailTemplate::PLACEHOLDER_PROJECT_CREATOR_FIRSTNAME, $project->getCreatedBy()->getFirstname(), $message);
            $message = str_replace(\App\Constant\MailTemplate::PLACEHOLDER_PROJECT_USER_LINK,
                $this->urlGenerator->generate(
                    'app.project.show',
                    ['id' => $project->getId()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
                $message
            );
            $message = str_replace(\App\Constant\MailTemplate::PLACEHOLDER_PROJECT_MANAGER_LINK,
                $this->urlGenerator->generate(
                    'app.project.show',
                    ['id' => $project->getId(), 'context' => 'call_of_project'],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
                $message
            );
        }

        if ($report instanceof Report) {
            $message = str_replace(\App\Constant\MailTemplate::PLACEHOLDER_REPORT_NAME, $report->getName(), $message);
            $message = str_replace(\App\Constant\MailTemplate::PLACEHOLDER_REPORT_DEADLINE, $report->getDeadline()->format('d/m/Y H:i'), $message);
            $message = str_replace(\App\Constant\MailTemplate::PLACEHOLDER_REPORT_LINK,
                $this->urlGenerator->generate(
                    'app.report.show',
                    ['id' => $report->getId()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
                $message
            );
        }

        if ($invitation instanceof Invitation) {
            $message = str_replace(\App\Constant\MailTemplate::PLACEHOLDER_URL_INVITATION,
                $this->urlGenerator->generate('app.process_after_shibboleth_connection',
                    ['token' => $invitation->getToken()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
                $message
            );
        }

        return $message;
    }

    /**
     * @param Report $report
     * @return void
     * @throws TransportExceptionInterface
     */
    public function notificationUserNewReporter(Report $report)
    {
        $this->sendMail(\App\Constant\MailTemplate::NOTIFICATION_USER_NEW_REPORTER, $report, $report->getReporter());
    }

    /**
     * @param Report $report
     * @return void
     * @throws TransportExceptionInterface
     */
    public function notificationUserNewReporters(Report $report)
    {
        static $reportersNotified = [];
        if (in_array($report->getReporter(), $reportersNotified)) {
            return;
        }
        $reportersNotified[] = $report->getReporter();

        $this->sendMail(\App\Constant\MailTemplate::NOTIFICATION_USER_NEW_REPORTERS, $report, $report->getReporter());
    }

    /**
     * @param Invitation $invitation
     * @return void
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function notificationUserInvitation(Invitation $invitation)
    {
        if ($invitation->getToken() === null) {
            throw new Exception('The token is null');
        }

        $this->sendMail(\App\Constant\MailTemplate::NOTIFICATION_USER_INVITATION, $invitation, $invitation->getUser());

        $invitation->setSentAt(new \DateTime());
    }

    /**
     * @param Project $project
     * @return void
     * @throws TransportExceptionInterface
     */
    public function notificationUserNewProject(Project $project)
    {
        $this->sendMail(\App\Constant\MailTemplate::NOTIFICATION_USER_NEW_PROJECT, $project, $project->getCreatedBy());
    }

    /**
     * @param Project $project
     * @return void
     * @throws TransportExceptionInterface
     */
    public function notificationCopFollowersNewProject(Project $project)
    {
        foreach ($project->getCallOfProject()->getSubscribers() as $recipient) {
            $this->sendMail(\App\Constant\MailTemplate::NOTIFICATION_COP_FOLLOWERS_NEW_PROJECT, $project, $recipient);
        }
    }

    /**
     * @param Report $report
     * @return void
     * @throws TransportExceptionInterface
     */
    public function notificationReporterReportUpdated(Report $report)
    {
        $this->sendMail(\App\Constant\MailTemplate::NOTIFICATION_REPORTER_REPORT_UPDATED, $report, $report->getReporter());
    }

    /**
     * @param Report $report
     * @return void
     * @throws TransportExceptionInterface
     */
    public function notificationCopFollowersReportUpdated(Report $report)
    {
        foreach ($report->getProject()->getCallOfProject()->getSubscribers() as $recipient) {
            $this->sendMail(\App\Constant\MailTemplate::NOTIFICATION_COP_FOLLOWERS_REPORT_UPDATED, $report, $recipient);
        }
    }

    /**
     * @param Project $project
     * @param string $mailTemplateName
     * @return void
     * @throws TransportExceptionInterface
     */
    private function notificationUserValidateRefuseProject(Project $project, string $mailTemplateName)
    {
        $mailTemplate = $this->getEmailTemplateFromCallOfProject($mailTemplateName, $project->getCallOfProject());
        if (!$mailTemplate instanceof MailTemplateInterface) return;

        $mailBody = empty($project->getValidateRejectMailContent()) ? $mailTemplate->getBody() : $project->getValidateRejectMailContent();

        $recipient = $project->getCreatedBy();
        if (!$recipient instanceof User or empty($recipient->getEmail())) return;

        $email = (new Email())
            ->from($this->mailFrom)
            ->to($recipient->getEmail())
            ->subject($mailTemplate->getSubject())
            ->html($this->parseMessageWithProject($mailBody, $project, $recipient));

        $this->mailer->send($email);
    }

    /**
     * @param Project $project
     * @return void
     * @throws TransportExceptionInterface
     */
    public function notificationUserValidationProject(Project $project)
    {
        $this->notificationUserValidateRefuseProject($project, \App\Constant\MailTemplate::NOTIFICATION_USER_VALIDATION_PROJECT);
    }

    /**
     * @param Project $project
     * @return void
     * @throws TransportExceptionInterface
     */
    public function notificationUserRefusalProject(Project $project)
    {
        $this->notificationUserValidateRefuseProject($project, \App\Constant\MailTemplate::NOTIFICATION_USER_REFUSAL_PROJECT);
    }
}
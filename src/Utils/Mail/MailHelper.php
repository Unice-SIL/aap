<?php


namespace App\Utils\Mail;


use App\Entity\CallOfProject;
use App\Entity\CallOfProjectMailTemplate;
use App\Entity\Interfaces\MailTemplateInterface;
use App\Entity\Invitation;
use App\Entity\MailTemplate;
use App\Entity\Project;
use App\Entity\Report;
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
     * @param string $message
     * @param Project $project
     * @return string|string[]
     */
    public function parseMessageWithProject(string $message, Project $project)
    {
        $owner = $project->getCreatedBy();
        $message = str_replace(\App\Constant\MailTemplate::PLACEHOLDER_FIRSTNAME, $owner->getFirstname(), $message);
        $message = str_replace(\App\Constant\MailTemplate::PLACEHOLDER_LASTNAME, $owner->getLastname(), $message);
        $message = str_replace(\App\Constant\MailTemplate::PLACEHOLDER_CALL_OF_PROJECT_NAME, $project->getCallOfProject()->getName(), $message);
        $message = str_replace(\App\Constant\MailTemplate::PLACEHOLDER_CALL_OF_PROJECT_MANAGER_LINK,
            $this->urlGenerator->generate(
                'app.call_of_project.informations',
                ['id' => $project->getCallOfProject()->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
            $message
        );
        $message = str_replace(\App\Constant\MailTemplate::PLACEHOLDER_PROJECT_NAME, $project->getName(), $message);
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

        return $message;
    }

    /**
     * @param Report $report
     * @return void
     * @throws TransportExceptionInterface
     */
    public function notificationUserNewReporter(Report $report)
    {
        $callOfProject = $report->getProject()->getCallOfProject();
        $mailTemplate = $this->getEmailTemplateFromCallOfProject(\App\Constant\MailTemplate::NOTIFICATION_USER_NEW_REPORTER, $callOfProject);

        if (!$mailTemplate instanceof MailTemplateInterface) return;
        if (!$mailTemplate->isEnable()) return;

        $to = $report->getReporter()->getEmail();

        if (empty($to)) return;

        $email = (new Email())
            ->from($this->mailFrom)
            ->to($to)
            ->subject($mailTemplate->getSubject())
            ->html($this->parseMessageWithProject($mailTemplate->getBody(), $report->getProject()));


        $this->mailer->send($email);
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

        $callOfProject = $report->getProject()->getCallOfProject();
        $mailTemplate = $this->getEmailTemplateFromCallOfProject(\App\Constant\MailTemplate::NOTIFICATION_USER_NEW_REPORTERS, $callOfProject);

        if (!$mailTemplate instanceof MailTemplateInterface) return;
        if (!$mailTemplate->isEnable()) return;

        $to = $report->getReporter()->getEmail();

        if (empty($to)) return;

        $email = (new Email())
            ->from($this->mailFrom)
            ->to($to)
            ->subject($mailTemplate->getSubject())
            ->html($this->parseMessageWithProject($mailTemplate->getBody(), $report->getProject()));

        $this->mailer->send($email);
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
        $url = $this->urlGenerator->generate('app.process_after_shibboleth_connection', ['token' => $invitation->getToken()], UrlGeneratorInterface::ABSOLUTE_URL);
        $user = $invitation->getUser();

        $mailTemplate = $this->getGenericEmailTemplate(\App\Constant\MailTemplate::NOTIFICATION_USER_INVITATION);

        if (!$mailTemplate instanceof MailTemplateInterface) return;
        if (!$mailTemplate->isEnable()) return;

        $to = $invitation->getUser()->getEmail();

        if (empty($to)) return;

        $content = $mailTemplate->getBody();
        $content = str_replace(\App\Constant\MailTemplate::PLACEHOLDER_FIRSTNAME, $user->getFirstname(), $content);
        $content = str_replace(\App\Constant\MailTemplate::PLACEHOLDER_LASTNAME, $user->getLastname(), $content);
        $content = str_replace(\App\Constant\MailTemplate::PLACEHOLDER_URL_INVITATION, $url, $content);

        $email = (new Email())
            ->from($this->mailFrom)
            ->to($to)
            ->subject($mailTemplate->getSubject())
            ->html($content);

        $this->mailer->send($email);

        $invitation->setSentAt(new \DateTime());
    }

    /**
     * @param Project $project
     * @return void
     * @throws TransportExceptionInterface
     */
    public function notificationUserNewProject(Project $project)
    {
        $callOfProject = $project->getCallOfProject();
        $mailTemplate = $this->getEmailTemplateFromCallOfProject(\App\Constant\MailTemplate::NOTIFICATION_USER_NEW_PROJECT, $callOfProject);

        if (!$mailTemplate instanceof MailTemplateInterface) return;
        if (!$mailTemplate->isEnable()) return;

        $to = $project->getCreatedBy()->getEmail();

        if (empty($to)) return;

        $email = (new Email())
            ->from($this->mailFrom)
            ->to($to)
            ->subject($mailTemplate->getSubject())
            ->html($this->parseMessageWithProject($mailTemplate->getBody(), $project));

        $this->mailer->send($email);
    }

    /**
     * @param Project $project
     * @return void
     * @throws TransportExceptionInterface
     */
    public function notificationCopFollowersNewProject(Project $project)
    {
        $callOfProject = $project->getCallOfProject();
        $mailTemplate = $this->getEmailTemplateFromCallOfProject(\App\Constant\MailTemplate::NOTIFICATION_COP_FOLLOWERS_NEW_PROJECT, $callOfProject);

        if (!$mailTemplate instanceof MailTemplateInterface) return;
        if (!$mailTemplate->isEnable()) return;

        $email = (new Email())
            ->from($this->mailFrom)
            ->subject($mailTemplate->getSubject())
            ->html($this->parseMessageWithProject($mailTemplate->getBody(), $project));

        foreach ($project->getCallOfProject()->getSubscribers() as $subscriber) {
            $address = $subscriber->getEmail();
            if (empty($address)) continue;
            $email->addTo($address);
        }

        if (empty($email->getTo())) return;

        $this->mailer->send($email);
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

        $to = $project->getCreatedBy()->getEmail();
        if (empty($to)) return;

        $email = (new Email())
            ->from($this->mailFrom)
            ->to($to)
            ->subject($mailTemplate->getSubject())
            ->html($this->parseMessageWithProject($mailBody, $project));

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
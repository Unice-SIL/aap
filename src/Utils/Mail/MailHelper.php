<?php


namespace App\Utils\Mail;


use App\Constant\MailTemplate;
use App\Entity\Invitation;
use App\Entity\Project;
use App\Entity\Report;
use App\Repository\MailTemplateRepository;
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
     * MailHelper constructor.
     * @param \Swift_Mailer $mailer
     * @param string $mailFrom
     * @param UrlGeneratorInterface $urlGenerator
     * @param MailTemplateRepository $mailTemplateRepository
     */
    public function __construct(
        \Swift_Mailer $mailer,
        string $mailFrom,
        UrlGeneratorInterface $urlGenerator,
        MailTemplateRepository $mailTemplateRepository
    )
    {
        $this->mailer = $mailer;
        $this->mailFrom = $mailFrom;
        $this->urlGenerator = $urlGenerator;
        $this->mailTemplateRepository = $mailTemplateRepository;
    }

    /**
     * @param string $message
     * @param Project $project
     * @return string|string[]
     */
    public static function parseValidationOrRefusalMessage(string $message, Project $project)
    {
        $owner = $project->getCreatedBy();
        $message = str_replace(MailTemplate::PLACEHOLDER_FIRSTNAME, $owner->getFirstname(), $message);
        $message = str_replace(MailTemplate::PLACEHOLDER_LASTNAME, $owner->getLastname(), $message);
        $message = str_replace(MailTemplate::PLACEHOLDER_CALL_OF_PROJECT_NAME, $project->getCallOfProject()->getName(), $message);
        $message = str_replace(MailTemplate::PLACEHOLDER_PROJECT_NAME, $project->getName(), $message);

        return $message;
    }

    /**
     * @param Report $report
     */
    public function notifyReporterAboutReport(Report $report)
    {
        $mailTemplate = $this->mailTemplateRepository->findOneByName(MailTemplate::NOTIFICATION_NEW_REPORT);
        $message = new \Swift_Message(
            $mailTemplate->getSubject(),
            self::parseValidationOrRefusalMessage($mailTemplate->getBody(), $report->getProject())
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
    public function notifyReporterAboutReports(Report $report)
    {

        static $reportersNotified = [];
        if (in_array($report->getReporter(), $reportersNotified)) {
            return;
        }
        $reportersNotified[] = $report->getReporter();

        $mailTemplate = $this->mailTemplateRepository->findOneByName(MailTemplate::NOTIFICATION_NEW_REPORTS);
        $message = new \Swift_Message(
            $mailTemplate->getSubject(),
            self::parseValidationOrRefusalMessage($mailTemplate->getBody(), $report->getProject())
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
     * @throws \Exception
     */
    public function sendInvitationMail(Invitation $invitation)
    {

        if ($invitation->getToken() === null) {
            throw new \Exception('The token is null');
        }
        $url = $this->urlGenerator->generate('app.process_after_shibboleth_connection', ['token' => $invitation->getToken()], UrlGeneratorInterface::ABSOLUTE_URL);
        $user = $invitation->getUser();
        $mailTemplate = $this->mailTemplateRepository->findOneByName(MailTemplate::INVITATION_MAIL);

        $message = str_replace(MailTemplate::PLACEHOLDER_FIRSTNAME, $user->getFirstname(), $mailTemplate->getBody());
        $message = str_replace(MailTemplate::PLACEHOLDER_LASTNAME, $user->getLastname(), $message);
        $message = str_replace(MailTemplate::PLACEHOLDER_URL_INVITATION, $url, $message);
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
}
<?php


namespace App\Utils\Mail;


use App\Constant\MailTemplate;
use App\Entity\Invitation;
use App\Entity\Project;
use App\Entity\Report;
use App\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

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
     * MailHelper constructor.
     * @param \Swift_Mailer $mailer
     * @param string $mailFrom
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(\Swift_Mailer $mailer, string $mailFrom, UrlGeneratorInterface $urlGenerator)
    {
        $this->mailer = $mailer;
        $this->mailFrom = $mailFrom;
        $this->urlGenerator = $urlGenerator;
    }

    public static function parseValidationOrRefusalMessage(string $message, Project $project)
    {
        $owner = $project->getCreatedBy();
        $message = str_replace(MailTemplate::PLACEHOLDER_FIRSTNAME, $owner->getFirstname(), $message);
        $message = str_replace(MailTemplate::PLACEHOLDER_LASTNAME, $owner->getLastname(), $message);
        $message = str_replace(MailTemplate::PLACEHOLDER_CALL_OF_PROJECT_NAME, $project->getCallOfProject()->getName(), $message);
        $message = str_replace(MailTemplate::PLACEHOLDER_PROJECT_NAME, $project->getName(), $message);

        return $message;
    }

    public function notifyReporterAboutReport(Report $report)
    {

        $message = new \Swift_Message(MailTemplate::NOTIFICATION_NEW_REPORT['subject'], sprintf(
            MailTemplate::NOTIFICATION_NEW_REPORT['body'],
            $report->getProject()->getName()
        ));
        $message
            ->setFrom($this->mailFrom)
            ->setTo($report->getReporter()->getEmail())
        ;

        $this->mailer->send($message);
    }

    public function notifyReporterAboutReports(Report $report)
    {

        static $reportersNotified = [];
        if (in_array($report->getReporter(), $reportersNotified)) {
            return;
        }
        $reportersNotified[] = $report->getReporter();

        $message = new \Swift_Message(MailTemplate::NOTIFICATION_NEW_REPORTS['subject'], sprintf(
            MailTemplate::NOTIFICATION_NEW_REPORTS['body'],
            $report->getProject()->getCallOfProject()->getName()
        ));
        $message
            ->setFrom($this->mailFrom)
            ->setTo($report->getReporter()->getEmail())
        ;

        $this->mailer->send($message);
    }

    public function sendInvitationMail(Invitation $invitation)
    {

        $url = $this->urlGenerator->generate('app.process_after_shibboleth_connection', ['token' => $invitation->getToken()], UrlGeneratorInterface::ABSOLUTE_URL);
        $message = new \Swift_Message(MailTemplate::INVITATION_MAIL['subject'], sprintf(
                MailTemplate::INVITATION_MAIL['body'],
                $url
            ));
        $message
            ->setFrom($this->mailFrom)
            ->setTo($invitation->getUser()->getEmail())
        ;

        $this->mailer->send($message);

        $invitation->setSentAt(new \DateTime());
    }
}
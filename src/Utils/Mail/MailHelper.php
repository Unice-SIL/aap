<?php


namespace App\Utils\Mail;


use App\Constant\MailTemplate;
use App\Entity\Project;
use App\Entity\Report;
use App\Entity\User;

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
     * MailHelper constructor.
     * @param \Swift_Mailer $mailer
     * @param string $mailFrom
     */
    public function __construct(\Swift_Mailer $mailer, string $mailFrom)
    {
        $this->mailer = $mailer;
        $this->mailFrom = $mailFrom;
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

        $message = new \Swift_Message(MailTemplate::NOTIFICATION_NEW_REPORTS['subject'], sprintf(
            MailTemplate::NOTIFICATION_NEW_REPORTS['body'],
            $report->getProject()->getCallOfProject()->getName()
        ) . $report->getReporter());
        $message
            ->setFrom($this->mailFrom)
            ->setTo($report->getReporter()->getEmail())
        ;

        $this->mailer->send($message);
    }
}
<?php


namespace App\Utils\Mail;


use App\Constant\MailTemplate;
use App\Entity\Project;

class MailHelper
{
    public static function parseValidationOrRefusalMessage(string $message, Project $project)
    {
        $owner = $project->getCreatedBy();
        $message = str_replace(MailTemplate::PLACEHOLDER_FIRSTNAME, $owner->getFirstname(), $message);
        $message = str_replace(MailTemplate::PLACEHOLDER_LASTNAME, $owner->getLastname(), $message);
        $message = str_replace(MailTemplate::PLACEHOLDER_CALL_OF_PROJECT_NAME, $project->getCallOfProject()->getName(), $message);
        $message = str_replace(MailTemplate::PLACEHOLDER_PROJECT_NAME, $project->getName(), $message);

        return $message;
    }
}
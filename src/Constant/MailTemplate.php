<?php


namespace App\Constant;


class MailTemplate
{
    const VALIDATION_MAIL = 'Bonjour ' . self::PLACEHOLDER_FIRSTNAME . ' ' . self::PLACEHOLDER_LASTNAME . ' </br>
                            Suite à l\'appel à projets ' . self::PLACEHOLDER_CALL_OF_PROJECT_NAME . ' vous nous avez fait une proposition avec
                            le projet ' . self::PLACEHOLDER_PROJECT_NAME . ' et nous vous remercions.
                            Nous sommes heureux de vous annoncer que votre proposition a été retenue. </br>
                            Nos sincères salutations';
    const REFUSAL_MAIL = 'Bonjour ' . self::PLACEHOLDER_FIRSTNAME . ' ' . self::PLACEHOLDER_LASTNAME . ' </br>
                            Suite à l\'appel à projets ' . self::PLACEHOLDER_CALL_OF_PROJECT_NAME . ' vous nous avez fait une proposition avec
                            le projet ' . self::PLACEHOLDER_PROJECT_NAME . ' et nous vous remercions.
                            Nous sommes dans le regret de vous annoncer que votre proposition a été refusée. </br>
                            Nos sincères salutations';

    const PLACEHOLDER_LASTNAME = '[__LASTNAME__]';
    const PLACEHOLDER_FIRSTNAME = '[__FIRSTNAME__]';
    const PLACEHOLDER_PROJECT_NAME = '[__PROJECT_NAME__]';
    const PLACEHOLDER_CALL_OF_PROJECT_NAME = '[__CALL_OF_PROJECT_NAME__]';

    const PLACEHOLDERS = [
        'app.mail_template.placeholder.creator_lastname' => self::PLACEHOLDER_LASTNAME,
        'app.mail_template.placeholder.creator_firstname' => self::PLACEHOLDER_FIRSTNAME,
        'app.mail_template.placeholder.project_name' => self::PLACEHOLDER_PROJECT_NAME,
        'app.mail_template.placeholder.call_of_project_name' => self::PLACEHOLDER_CALL_OF_PROJECT_NAME,
    ];

    const NOTIFICATION_NEW_REPORT = [
        'subject' => 'Nouveau rapport',
        'body' => 'Vous avez était identifié comme rapporteur sur le projet %s',
    ];

    const NOTIFICATION_NEW_REPORTS = [
        'subject' => 'Nouveaux rapports',
        'body' => 'Vous avez était identifié comme rapporteur sur plusieurs projets de l\'appel à projet %s',
    ];
}
<?php


namespace App\Constant;


class MailTemplate
{
    const VALIDATION_PROJECT = 'validation de projet';

    const REFUSAL_PROJECT = 'refus de projet';


    const PLACEHOLDER_LASTNAME = '[__LASTNAME__]';
    const PLACEHOLDER_FIRSTNAME = '[__FIRSTNAME__]';
    const PLACEHOLDER_PROJECT_NAME = '[__PROJECT_NAME__]';
    const PLACEHOLDER_CALL_OF_PROJECT_NAME = '[__CALL_OF_PROJECT_NAME__]';
    const PLACEHOLDER_URL_INVITATION = '[__URL_INVITATION__]';

    const PLACEHOLDERS = [
        'app.mail_template.placeholder.creator_lastname' => self::PLACEHOLDER_LASTNAME,
        'app.mail_template.placeholder.creator_firstname' => self::PLACEHOLDER_FIRSTNAME,
        'app.mail_template.placeholder.project_name' => self::PLACEHOLDER_PROJECT_NAME,
        'app.mail_template.placeholder.call_of_project_name' => self::PLACEHOLDER_CALL_OF_PROJECT_NAME,
    ];

    const NOTIFICATION_NEW_REPORT = 'notification pour un nouveau rapport';

    const NOTIFICATION_NEW_REPORTS = 'notification pour ajout de rapport en masse';

    const INVITATION_MAIL = 'inviter un utilisateur';

    const NOTIFY_CREATOR_OF_A_NEW_PROJECT = 'notification à destination du créateur d\'un nouveau projet';
}
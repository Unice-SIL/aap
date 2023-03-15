<?php


namespace App\Constant;


class MailTemplate
{
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

    const VALIDATION_PROJECT = 'app.mail_template.validation_project';
    const REFUSAL_PROJECT = 'app.mail_template.refusal_project';
    const NOTIFICATION_NEW_REPORT = 'app.mail_template.notification_new_report';
    const NOTIFICATION_NEW_REPORTS = 'app.mail_template.notification_new_reports';
    const INVITATION_MAIL = 'app.mail_template.invite_a_user';
    const NOTIFY_CREATOR_OF_A_NEW_PROJECT = 'app.mail_template.notification_creator_of_a_new_project';
    const NOTIFY_MANAGERS_OF_A_NEW_PROJECT = 'app.mail_template.notify_managers_of_a_new_project';
}

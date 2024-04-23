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

    const NOTIFICATION_USER_VALIDATION_PROJECT = 'app.mail_template.user.validation_project';
    const NOTIFICATION_USER_REFUSAL_PROJECT = 'app.mail_template.user.refusal_project';
    const NOTIFICATION_USER_NEW_REPORTER = 'app.mail_template.user.new_reporter';
    const NOTIFICATION_USER_NEW_REPORTERS = 'app.mail_template.user.new_reporters';
    const NOTIFICATION_USER_INVITATION = 'app.mail_template.user.invitation';
    const NOTIFICATION_USER_NEW_PROJECT = 'app.mail_template.user.new_project';
    const NOTIFICATION_COP_FOLLOWERS_NEW_PROJECT = 'app.mail_template.cop_followers.new_project';
}
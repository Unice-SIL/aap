<?php


namespace App\Constant;


class MailTemplate
{
    const PLACEHOLDER_LASTNAME = '[__LASTNAME__]';
    const PLACEHOLDER_FIRSTNAME = '[__FIRSTNAME__]';
    const PLACEHOLDER_PROJECT_NAME = '[__PROJECT_NAME__]';
    const PLACEHOLDER_PROJECT_CREATOR_LASTNAME = '[__PROJECT_CREATOR_LASTNAME__]';
    const PLACEHOLDER_PROJECT_CREATOR_FIRSTNAME = '[__PROJECT_CREATOR_FIRSTNAME__]';
    const PLACEHOLDER_PROJECT_USER_LINK = '[__PROJECT_USER_LINK__]';
    const PLACEHOLDER_PROJECT_MANAGER_LINK = '[__PROJECT_MANAGER_LINK__]';
    const PLACEHOLDER_CALL_OF_PROJECT_NAME = '[__CALL_OF_PROJECT_NAME__]';
    const PLACEHOLDER_CALL_OF_PROJECT_MANAGER_LINK = '[__CALL_OF_PROJECT_MANAGER_LINK__]';
    const PLACEHOLDER_REPORT_NAME = '[__PLACEHOLDER_REPORT_NAME__]';
    const PLACEHOLDER_REPORT_LINK = '[__PLACEHOLDER_REPORT_LINK__]';
    const PLACEHOLDER_REPORT_DEADLINE = '[__PLACEHOLDER_REPORT_DEADLINE__]';
    const PLACEHOLDER_URL_INVITATION = '[__URL_INVITATION__]';

    const PLACEHOLDERS = [
        'app.mail_template.placeholder.recipient_lastname' => self::PLACEHOLDER_LASTNAME,
        'app.mail_template.placeholder.recipient_firstname' => self::PLACEHOLDER_FIRSTNAME,
        'app.mail_template.placeholder.project_name' => self::PLACEHOLDER_PROJECT_NAME,
        'app.mail_template.placeholder.project_creator_lastname' => self::PLACEHOLDER_PROJECT_CREATOR_LASTNAME,
        'app.mail_template.placeholder.project_creator_firstname' => self::PLACEHOLDER_PROJECT_CREATOR_FIRSTNAME,
        'app.mail_template.placeholder.project_user_link' => self::PLACEHOLDER_PROJECT_USER_LINK,
        'app.mail_template.placeholder.project_manager_link' => self::PLACEHOLDER_PROJECT_MANAGER_LINK,
        'app.mail_template.placeholder.call_of_project_name' => self::PLACEHOLDER_CALL_OF_PROJECT_NAME,
        'app.mail_template.placeholder.call_of_project_manager_link' => self::PLACEHOLDER_CALL_OF_PROJECT_MANAGER_LINK,
        'app.mail_template.placeholder.report_name' => self::PLACEHOLDER_REPORT_NAME,
        'app.mail_template.placeholder.report_link' => self::PLACEHOLDER_REPORT_LINK,
        'app.mail_template.placeholder.report_deadline' => self::PLACEHOLDER_REPORT_DEADLINE,
        'app.mail_template.placeholder.invitation_link' => self::PLACEHOLDER_URL_INVITATION,
    ];

    const NOTIFICATION_USER_VALIDATION_PROJECT = 'app.mail_template.user.validation_project';
    const NOTIFICATION_USER_REFUSAL_PROJECT = 'app.mail_template.user.refusal_project';
    const NOTIFICATION_USER_NEW_REPORTER = 'app.mail_template.user.new_reporter';
    const NOTIFICATION_USER_NEW_REPORTERS = 'app.mail_template.user.new_reporters';
    const NOTIFICATION_REPORTER_REPORT_UPDATED = 'app.mail_template.reporter.report_updated';
    const NOTIFICATION_COP_FOLLOWERS_REPORT_UPDATED = 'app.mail_template.cop_followers.report_updated';
    const NOTIFICATION_USER_INVITATION = 'app.mail_template.user.invitation';
    const NOTIFICATION_USER_NEW_PROJECT = 'app.mail_template.user.new_project';
    const NOTIFICATION_COP_FOLLOWERS_NEW_PROJECT = 'app.mail_template.cop_followers.new_project';
}
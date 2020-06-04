<?php


namespace App\Constant;


class MailTemplate
{
    const VALIDATION_MAIL = 'Bonjour [__FIRSTNAME__] [__LASTNAME__] </br>
                            Suite à l\'appel à projets [__CALL_OF_PROJECT_NAME__] vous nous avez fait une proposition avec
                            le projet [__PROJECT_NAME__] et nous vous remercions.
                            Nous sommes heureux de vous annoncer que votre proposition a été retenue. </br>
                            Nos sincères salutations';
    const REFUSAL_MAIL = 'Bonjour [__FIRSTNAME__] [__LASTNAME__] </br>
                            Suite à l\'appel à projets [__CALL_OF_PROJECT_NAME__] vous nous avez fait une proposition avec
                            le projet [__PROJECT_NAME__] et nous vous remercions.
                            Nous sommes dans le regret de vous annoncer que votre proposition a été refusée. </br>
                            Nos sincères salutations';

    const PLACEHOLDERS = [
        'app.mail_template.placeholder.creator_lastname' => '[__LASTNAME__]',
        'app.mail_template.placeholder.creator_firstname' => '[__FIRSTNAME__]',
        'app.mail_template.placeholder.project_name' => '[__PROJECT_NAME__]',
        'app.mail_template.placeholder.call_of_project_name' => '[__CALL_OF_PROJECT_NAME__]',
    ];
}
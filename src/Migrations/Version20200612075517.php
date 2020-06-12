<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Constant\MailTemplate;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200612075517 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE mail_template (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, subject VARCHAR(255) DEFAULT NULL, body LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql("INSERT INTO mail_template (name, subject, body) VALUES 
            (
                '" . MailTemplate::VALIDATION_PROJECT. "',
                'Validation de projet',
                'Bonjour ". MailTemplate::PLACEHOLDER_FIRSTNAME . " " . MailTemplate::PLACEHOLDER_LASTNAME . " </br>
                Suite à l\'appel à projets " . MailTemplate::PLACEHOLDER_CALL_OF_PROJECT_NAME . " vous nous avez fait une proposition avec
                le projet " . MailTemplate::PLACEHOLDER_PROJECT_NAME . " et nous vous remercions.
                Nous sommes heureux de vous annoncer que votre proposition a été retenue. </br>
                Nos sincères salutations'
            ),
            (
                '" . MailTemplate::REFUSAL_PROJECT. "',
                'Refus de projet',
                'Bonjour ". MailTemplate::PLACEHOLDER_FIRSTNAME . " " . MailTemplate::PLACEHOLDER_LASTNAME . " </br>
                Suite à l\'appel à projets " . MailTemplate::PLACEHOLDER_CALL_OF_PROJECT_NAME . " vous nous avez fait une proposition avec
                le projet " . MailTemplate::PLACEHOLDER_PROJECT_NAME . " et nous vous remercions.
                Nous sommes dans le regret de vous annoncer que votre proposition a été refusée. </br>
                Nos sincères salutations'
            ),
            (
                '" . MailTemplate::NOTIFICATION_NEW_REPORT. "',
                'Nouveau rapport',
                'Vous avez été identifié comme rapporteur sur le projet " . MailTemplate::PLACEHOLDER_PROJECT_NAME. "'
            ),
            (
                    '" . MailTemplate::NOTIFICATION_NEW_REPORTS. "',
                'Nouveaux rapports',
                'Vous avez été identifié comme rapporteur sur un ou plusieurs projets de l\'appel à projet " . MailTemplate::PLACEHOLDER_CALL_OF_PROJECT_NAME. "'
            ),
            (
                '" . MailTemplate::INVITATION_MAIL. "',
                'Inivtation à vous connecter',
                'Vous avez été invité à créer un compte. Rendez-vous à l\'adresse " . MailTemplate::PLACEHOLDER_URL_INVITATION .  " et connectez-vous avec vos identifiants UCA'
            )
        ");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE mail_template');
    }
}

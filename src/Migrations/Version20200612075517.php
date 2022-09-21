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
        $this->addSql('INSERT INTO mail_template (name, subject, body) VALUES 
            (
                "' . MailTemplate::VALIDATION_PROJECT. '",
                "[UCA - AAP] Proposition de projet retenue",
                "<p>Bonjour ' . MailTemplate::PLACEHOLDER_FIRSTNAME . ',</p><p>Suite à l\'appel à projets ""' . MailTemplate::PLACEHOLDER_CALL_OF_PROJECT_NAME . '"" vous nous avez fait une proposition avec le projet ""' . MailTemplate::PLACEHOLDER_PROJECT_NAME . '"" et nous vous remercions. Nous sommes heureux de vous annoncer que votre proposition a été retenue.</p><p>Bien cordialement.</p>"
            ),
            (
                "' . MailTemplate::REFUSAL_PROJECT. '",
                "[UCA - AAP]  Proposition de projet non retenue",
                "<p>Bonjour ' . MailTemplate::PLACEHOLDER_FIRSTNAME . ',</p><p>Suite à l\'appel à projets ""' . MailTemplate::PLACEHOLDER_CALL_OF_PROJECT_NAME . '"" vous nous avez fait une proposition avec le projet ""' . MailTemplate::PLACEHOLDER_PROJECT_NAME . '"" et nous vous remercions. Nous sommes malheureusement dans le regret de vous annoncer que votre proposition n\'a pas été retenue.</p><p>Bien cordialement.</p>"
            ),
            (
                "' . MailTemplate::NOTIFICATION_NEW_REPORT. '",
                "[UCA - AAP] Vous êtes nommé comme rapporteur",
                "<p>Bonjour ' . MailTemplate::PLACEHOLDER_FIRSTNAME . ',</p><p>Vous avez été identifié comme rapporteur pour le projet ""' . MailTemplate::PLACEHOLDER_PROJECT_NAME . '"" sur la plateforme Appel à Projet de Université Côte d\'Azur.</p><p>Bien cordialement.</p>"
            ),
            (
                "' . MailTemplate::NOTIFICATION_NEW_REPORTS. '",
                "[UCA - AAP] Vous êtes nommé comme rapporteur",
                "<p>Bonjour ' . MailTemplate::PLACEHOLDER_FIRSTNAME . ',</p><p>Vous avez été identifié comme rapporteur sur un ou plusieurs projets de l\'appel à projet ""' . MailTemplate::PLACEHOLDER_CALL_OF_PROJECT_NAME . '"".</p><p>Bien cordialement.</p>"
            ),
            (
                "' . MailTemplate::INVITATION_MAIL. '",
                "[UCA - AAP] Invitation à vous connecter",
                "<p>Bonjour ' . MailTemplate::PLACEHOLDER_FIRSTNAME . ',</p><p>Vous avez été invité à créer un compte sur la plateforme d\'appel à projets de Université Côte d\'Azur. Rendez-vous à l\'adresse [__URL_INVITATION__] et connectez-vous avec vos identifiants UCA.</p><p>Bien cordialement.</p>"
            )
        ');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE mail_template');
    }
}

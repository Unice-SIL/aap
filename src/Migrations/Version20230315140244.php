<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Constant\MailTemplate;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230315140244 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE call_of_project_mail_template (id INT AUTO_INCREMENT NOT NULL, call_of_project_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', is_automatic_sending_mail TINYINT(1) NOT NULL, subject VARCHAR(255) DEFAULT NULL, body LONGTEXT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_6DD9FF77866F139A (call_of_project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE call_of_project_mail_template ADD CONSTRAINT FK_6DD9FF77866F139A FOREIGN KEY (call_of_project_id) REFERENCES call_of_project (id)');
        $this->addSql('ALTER TABLE call_of_project DROP validation_mail_template, DROP refusal_mail_template, DROP is_automatic_sending_validation_mail, DROP is_automatic_sending_refusal_mail');
        $this->addSql('UPDATE mail_template SET name="' . MailTemplate::VALIDATION_PROJECT . '" WHERE name="Validation de projet"');
        $this->addSql('UPDATE mail_template SET name="' . MailTemplate::REFUSAL_PROJECT . '" WHERE name="Refus de projet"');
        $this->addSql('UPDATE mail_template SET name="' . MailTemplate::NOTIFICATION_NEW_REPORT . '" WHERE name="Notification pour un nouveau rapport"');
        $this->addSql('UPDATE mail_template SET name="' . MailTemplate::NOTIFICATION_NEW_REPORTS . '" WHERE name="Notification pour ajout de rapport en masse"');
        $this->addSql('UPDATE mail_template SET name="' . MailTemplate::INVITATION_MAIL . '" WHERE name="Inviter un utilisateur"');
        $this->addSql('UPDATE mail_template SET name="' . MailTemplate::NOTIFY_CREATOR_OF_A_NEW_PROJECT . '" WHERE name="Notification à destination du créateur d\'un nouveau projet"');
        $this->addSql('UPDATE mail_template SET name="' . MailTemplate::NOTIFY_MANAGERS_OF_A_NEW_PROJECT . '" WHERE name="Notification à destination des gestionnaires d\'un nouveau projet"');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE call_of_project_mail_template');
        $this->addSql('ALTER TABLE call_of_project ADD validation_mail_template LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD refusal_mail_template LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD is_automatic_sending_validation_mail TINYINT(1) NOT NULL, ADD is_automatic_sending_refusal_mail TINYINT(1) NOT NULL');
    }
}

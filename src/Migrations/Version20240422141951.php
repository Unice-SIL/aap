<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Constant\MailTemplate;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240422141951 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE mail_template ADD enable TINYINT(1) DEFAULT \'1\' NOT NULL');
        $this->addSql('INSERT INTO mail_template (name, subject, body) VALUES 
            (
                "' . MailTemplate::NOTIFICATION_REPORTER_UPDATE_REPORT. '",
                "[UCA - AAP] Modification de votre rapport",
                "<p>Bonjour ' . MailTemplate::PLACEHOLDER_FIRSTNAME . ',</p><p>Les modifications que vous venez d\'apporter à votre rapport pour le projet ""' . MailTemplate::PLACEHOLDER_PROJECT_NAME . '"" ont bien été enregistré. Vous avez la possibilité de le modifier de nouveau jusqu\'à la date butoir le ""' . MailTemplate::PLACEHOLDER_REPORT_DEADLINE . '"".</p><p>Bien cordialement.</p>"
            )
            ');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE mail_template DROP enable');
        $this->addSql('DELETE FROM mail_template WHERE name = "'. MailTemplate::NOTIFICATION_REPORTER_UPDATE_REPORT .'"');
    }
}

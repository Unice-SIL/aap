<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Constant\MailTemplate;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200617122857 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('INSERT INTO mail_template (name, subject, body) VALUES
            (
                "' . MailTemplate::NOTIFY_CREATOR_OF_A_NEW_PROJECT . '",
                "[UCA - AAP] Vous avez déposé un nouveau projet",
                "<p>Bonjour ' . MailTemplate::PLACEHOLDER_FIRSTNAME . ',</p><p>Votre proposition de projet ""' . MailTemplate::PLACEHOLDER_PROJECT_NAME . '"" a bien été enregistré sur la plateforme. Elle sera étudiée une fois la date butoir de dépôt de l\'appel atteinte, et une réponse vous sera apportée au plus vite. En attendant, vous avez la possibilité de modifier votre proposition autant que nécessaire jusqu\'à la fermeture de l\'appel à projets.</p><p>Bien cordialement.</p>"
            )

        ');

        $this->addSql('INSERT INTO mail_template (name, subject, body) VALUES
            (
                "' . MailTemplate::NOTIFY_MANAGERS_OF_A_NEW_PROJECT . '",
                "[UCA - AAP] Un nouveau projet a été créé",
                "<p>Bonjour ' . MailTemplate::PLACEHOLDER_FIRSTNAME . ',</p><p>le projet ""' . MailTemplate::PLACEHOLDER_PROJECT_NAME . '"" a été déposé pour l\'appel ""' . MailTemplate::PLACEHOLDER_CALL_OF_PROJECT_NAME . '"".</p><p>Bien cordialement.</p>"
            )

        ');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE call_of_project DROP publication_date');
    }
}

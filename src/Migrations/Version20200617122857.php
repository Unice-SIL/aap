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
                "Vous avez créer un nouveau projet",
                "Bonjour votre projet ' . MailTemplate::PLACEHOLDER_PROJECT_NAME . ' a bien été enregistré..."
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

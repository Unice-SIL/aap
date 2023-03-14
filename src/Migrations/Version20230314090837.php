<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230314090837 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE call_of_project_mail_template ADD call_of_project_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE call_of_project_mail_template ADD CONSTRAINT FK_6DD9FF77866F139A FOREIGN KEY (call_of_project_id) REFERENCES call_of_project (id)');
        $this->addSql('CREATE INDEX IDX_6DD9FF77866F139A ON call_of_project_mail_template (call_of_project_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE call_of_project_mail_template DROP FOREIGN KEY FK_6DD9FF77866F139A');
        $this->addSql('DROP INDEX IDX_6DD9FF77866F139A ON call_of_project_mail_template');
        $this->addSql('ALTER TABLE call_of_project_mail_template DROP call_of_project_id');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200515130314 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE call_of_project ADD organizing_center_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE call_of_project ADD CONSTRAINT FK_F65960C5718B7476 FOREIGN KEY (organizing_center_id) REFERENCES organizing_center (id)');
        $this->addSql('CREATE INDEX IDX_F65960C5718B7476 ON call_of_project (organizing_center_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE call_of_project DROP FOREIGN KEY FK_F65960C5718B7476');
        $this->addSql('DROP INDEX IDX_F65960C5718B7476 ON call_of_project');
        $this->addSql('ALTER TABLE call_of_project DROP organizing_center_id');
    }
}

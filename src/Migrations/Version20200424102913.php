<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200424102913 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE project_content DROP FOREIGN KEY FK_68DB3CCC635F0E4C');
        $this->addSql('ALTER TABLE project_content DROP number_value, DROP date_value, CHANGE project_form_widget_id project_form_widget_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE text_value content LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE project_content ADD CONSTRAINT FK_68DB3CCC635F0E4C FOREIGN KEY (project_form_widget_id) REFERENCES project_form_widget (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE project_content DROP FOREIGN KEY FK_68DB3CCC635F0E4C');
        $this->addSql('ALTER TABLE project_content ADD number_value DOUBLE PRECISION DEFAULT NULL, ADD date_value DATETIME DEFAULT NULL, CHANGE project_form_widget_id project_form_widget_id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', CHANGE content text_value LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE project_content ADD CONSTRAINT FK_68DB3CCC635F0E4C FOREIGN KEY (project_form_widget_id) REFERENCES project_form_widget (id)');
    }
}

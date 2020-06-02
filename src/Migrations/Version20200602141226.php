<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200602141226 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE acl ADD groupe_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE user_id user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE acl ADD CONSTRAINT FK_BC806D127A45358C FOREIGN KEY (groupe_id) REFERENCES `group` (id)');
        $this->addSql('CREATE INDEX IDX_BC806D127A45358C ON acl (groupe_id)');
        $this->addSql('CREATE UNIQUE INDEX uc_groupe_permission ON acl (groupe_id, permission, common_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE acl DROP FOREIGN KEY FK_BC806D127A45358C');
        $this->addSql('DROP INDEX IDX_BC806D127A45358C ON acl');
        $this->addSql('DROP INDEX uc_groupe_permission ON acl');
        $this->addSql('ALTER TABLE acl DROP groupe_id, CHANGE user_id user_id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\'');
    }
}

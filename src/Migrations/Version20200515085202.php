<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200515085202 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE common_acl (common_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', acl_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_F400DE688DBC56F7 (common_id), INDEX IDX_F400DE6844082458 (acl_id), PRIMARY KEY(common_id, acl_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acl (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', permission VARCHAR(60) NOT NULL, INDEX IDX_BC806D12A76ED395 (user_id), UNIQUE INDEX uc_user_permission (user_id, permission), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE common_acl ADD CONSTRAINT FK_F400DE688DBC56F7 FOREIGN KEY (common_id) REFERENCES common (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE common_acl ADD CONSTRAINT FK_F400DE6844082458 FOREIGN KEY (acl_id) REFERENCES acl (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acl ADD CONSTRAINT FK_BC806D12A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE common_acl DROP FOREIGN KEY FK_F400DE6844082458');
        $this->addSql('DROP TABLE common_acl');
        $this->addSql('DROP TABLE acl');
    }
}

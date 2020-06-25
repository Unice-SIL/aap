<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200625071415 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE dictionary (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dictionary_content (id INT AUTO_INCREMENT NOT NULL, dictionary_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', keyy VARCHAR(255) NOT NULL, value VARCHAR(255) DEFAULT NULL, INDEX IDX_1FA02208AF5E5B3C (dictionary_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE dictionary ADD CONSTRAINT FK_1FA0E526BF396750 FOREIGN KEY (id) REFERENCES common (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dictionary_content ADD CONSTRAINT FK_1FA02208AF5E5B3C FOREIGN KEY (dictionary_id) REFERENCES dictionary (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE dictionary_content DROP FOREIGN KEY FK_1FA02208AF5E5B3C');
        $this->addSql('DROP TABLE dictionary');
        $this->addSql('DROP TABLE dictionary_content');
    }
}

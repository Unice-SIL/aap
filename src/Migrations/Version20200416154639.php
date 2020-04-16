<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200416154639 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE call_of_project (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', created_by_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', updated_by_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_F65960C5B03A8386 (created_by_id), INDEX IDX_F65960C5896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', created_by_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', updated_by_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', call_of_project_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_2FB3D0EEB03A8386 (created_by_id), INDEX IDX_2FB3D0EE896DBBDE (updated_by_id), INDEX IDX_2FB3D0EE866F139A (call_of_project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_content (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', project_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', project_form_widget_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', text_value LONGTEXT DEFAULT NULL, number_value DOUBLE PRECISION DEFAULT NULL, date_value DATETIME DEFAULT NULL, INDEX IDX_68DB3CCC166D1F9C (project_id), INDEX IDX_68DB3CCC635F0E4C (project_form_widget_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_form_layout (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', call_of_project_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(100) DEFAULT NULL, is_template TINYINT(1) NOT NULL, INDEX IDX_F01B48B7866F139A (call_of_project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_form_widget (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', project_form_layout_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', position INT NOT NULL, widget LONGTEXT NOT NULL, INDEX IDX_4FD83D85149521B4 (project_form_layout_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', username VARCHAR(180) NOT NULL, email VARCHAR(255) DEFAULT NULL, firstname VARCHAR(100) DEFAULT NULL, lastname VARCHAR(100) DEFAULT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE call_of_project ADD CONSTRAINT FK_F65960C5B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE call_of_project ADD CONSTRAINT FK_F65960C5896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EEB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE866F139A FOREIGN KEY (call_of_project_id) REFERENCES call_of_project (id)');
        $this->addSql('ALTER TABLE project_content ADD CONSTRAINT FK_68DB3CCC166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE project_content ADD CONSTRAINT FK_68DB3CCC635F0E4C FOREIGN KEY (project_form_widget_id) REFERENCES project_form_widget (id)');
        $this->addSql('ALTER TABLE project_form_layout ADD CONSTRAINT FK_F01B48B7866F139A FOREIGN KEY (call_of_project_id) REFERENCES call_of_project (id)');
        $this->addSql('ALTER TABLE project_form_widget ADD CONSTRAINT FK_4FD83D85149521B4 FOREIGN KEY (project_form_layout_id) REFERENCES project_form_layout (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE866F139A');
        $this->addSql('ALTER TABLE project_form_layout DROP FOREIGN KEY FK_F01B48B7866F139A');
        $this->addSql('ALTER TABLE project_content DROP FOREIGN KEY FK_68DB3CCC166D1F9C');
        $this->addSql('ALTER TABLE project_form_widget DROP FOREIGN KEY FK_4FD83D85149521B4');
        $this->addSql('ALTER TABLE project_content DROP FOREIGN KEY FK_68DB3CCC635F0E4C');
        $this->addSql('ALTER TABLE call_of_project DROP FOREIGN KEY FK_F65960C5B03A8386');
        $this->addSql('ALTER TABLE call_of_project DROP FOREIGN KEY FK_F65960C5896DBBDE');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EEB03A8386');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE896DBBDE');
        $this->addSql('DROP TABLE call_of_project');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE project_content');
        $this->addSql('DROP TABLE project_form_layout');
        $this->addSql('DROP TABLE project_form_widget');
        $this->addSql('DROP TABLE user');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200409083048 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE project_form_layout (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(100) NOT NULL, is_template TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE call_of_project (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', project_form_layout_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', description LONGTEXT NOT NULL, INDEX IDX_F65960C5149521B4 (project_form_layout_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_content (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', project_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', project_form_widget_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', text_value LONGTEXT DEFAULT NULL, number_value DOUBLE PRECISION DEFAULT NULL, date_value DATETIME DEFAULT NULL, INDEX IDX_68DB3CCC166D1F9C (project_id), INDEX IDX_68DB3CCC635F0E4C (project_form_widget_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_form_widget (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', project_form_layout_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', position INT NOT NULL, widget LONGTEXT NOT NULL, INDEX IDX_4FD83D85149521B4 (project_form_layout_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', call_of_project_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_2FB3D0EE866F139A (call_of_project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE call_of_project ADD CONSTRAINT FK_F65960C5149521B4 FOREIGN KEY (project_form_layout_id) REFERENCES project_form_layout (id)');
        $this->addSql('ALTER TABLE project_content ADD CONSTRAINT FK_68DB3CCC166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE project_content ADD CONSTRAINT FK_68DB3CCC635F0E4C FOREIGN KEY (project_form_widget_id) REFERENCES project_form_widget (id)');
        $this->addSql('ALTER TABLE project_form_widget ADD CONSTRAINT FK_4FD83D85149521B4 FOREIGN KEY (project_form_layout_id) REFERENCES project_form_layout (id)');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE866F139A FOREIGN KEY (call_of_project_id) REFERENCES call_of_project (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE call_of_project DROP FOREIGN KEY FK_F65960C5149521B4');
        $this->addSql('ALTER TABLE project_form_widget DROP FOREIGN KEY FK_4FD83D85149521B4');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE866F139A');
        $this->addSql('ALTER TABLE project_content DROP FOREIGN KEY FK_68DB3CCC635F0E4C');
        $this->addSql('ALTER TABLE project_content DROP FOREIGN KEY FK_68DB3CCC166D1F9C');
        $this->addSql('DROP TABLE project_form_layout');
        $this->addSql('DROP TABLE call_of_project');
        $this->addSql('DROP TABLE project_content');
        $this->addSql('DROP TABLE project_form_widget');
        $this->addSql('DROP TABLE project');
    }
}

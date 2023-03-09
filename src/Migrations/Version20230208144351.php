<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230208144351 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove comment field';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE project DROP comment');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE project ADD comment LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}

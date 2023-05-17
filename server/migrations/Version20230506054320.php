<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230506054320 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE task CHANGE init_points init_points INT DEFAULT 0 NOT NULL, CHANGE matrix matrix JSON DEFAULT NULL, CHANGE flag_matrix flag_matrix VARCHAR(255) DEFAULT \'платёжная матрица\' NOT NULL, CHANGE chance chance JSON DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE task CHANGE init_points init_points INT NOT NULL, CHANGE matrix matrix JSON NOT NULL, CHANGE flag_matrix flag_matrix VARCHAR(32) DEFAULT \'платёжная матрица\' NOT NULL, CHANGE chance chance JSON NOT NULL');
    }
}

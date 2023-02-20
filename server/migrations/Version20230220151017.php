<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230220151017 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE task_mark ADD task_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE task_mark ADD CONSTRAINT FK_15C7660A8DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_15C7660A8DB60186 ON task_mark (task_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE task_mark DROP FOREIGN KEY FK_15C7660A8DB60186');
        $this->addSql('DROP INDEX IDX_15C7660A8DB60186 ON task_mark');
        $this->addSql('ALTER TABLE task_mark DROP task_id');
    }
}

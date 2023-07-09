<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230708070123 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE play_task (id INT AUTO_INCREMENT NOT NULL, task_id INT DEFAULT NULL, user_id INT NOT NULL, moves JSON NOT NULL, total_score INT DEFAULT NULL, count_tries INT DEFAULT NULL, success TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_597B25F48DB60186 (task_id), INDEX IDX_597B25F4A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE play_task ADD CONSTRAINT FK_597B25F48DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE play_task ADD CONSTRAINT FK_597B25F4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE play_task DROP FOREIGN KEY FK_597B25F48DB60186');
        $this->addSql('ALTER TABLE play_task DROP FOREIGN KEY FK_597B25F4A76ED395');
        $this->addSql('DROP TABLE play_task');
    }
}

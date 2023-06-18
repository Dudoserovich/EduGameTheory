<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230618033139 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_education_tasks (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, edu_tasks_id INT DEFAULT NULL, success TINYINT(1) NOT NULL, is_current_block TINYINT(1) NOT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_FE0813BFA76ED395 (user_id), INDEX IDX_FE0813BF10A70AEE (edu_tasks_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_education_tasks ADD CONSTRAINT FK_FE0813BFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE user_education_tasks ADD CONSTRAINT FK_FE0813BF10A70AEE FOREIGN KEY (edu_tasks_id) REFERENCES education_tasks (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_education_tasks DROP FOREIGN KEY FK_FE0813BFA76ED395');
        $this->addSql('ALTER TABLE user_education_tasks DROP FOREIGN KEY FK_FE0813BF10A70AEE');
        $this->addSql('DROP TABLE user_education_tasks');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221113065623 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE achievement (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(32) NOT NULL, description VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_96737FF15E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE education (id INT AUTO_INCREMENT NOT NULL, topic_id INT DEFAULT NULL, name VARCHAR(32) NOT NULL, deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_DB0A5ED25E237E06 (name), INDEX IDX_DB0A5ED21F55203D (topic_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE education_tasks (id INT AUTO_INCREMENT NOT NULL, edu_id INT DEFAULT NULL, task_id INT DEFAULT NULL, text VARCHAR(255) NOT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_ECD47F3040A8A24B (edu_id), INDEX IDX_ECD47F308DB60186 (task_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task (id INT AUTO_INCREMENT NOT NULL, topic_id INT DEFAULT NULL, owner_id INT DEFAULT NULL, name VARCHAR(32) NOT NULL, type VARCHAR(32) DEFAULT \'system\' NOT NULL, deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_527EDB255E237E06 (name), INDEX IDX_527EDB251F55203D (topic_id), INDEX IDX_527EDB257E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task_mark (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, rating INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_15C7660AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE term (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(32) NOT NULL, description VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_A50FE78D5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE topic (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(32) NOT NULL, deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_9D40DE1B5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_achievement (id INT AUTO_INCREMENT NOT NULL, achievement_id INT NOT NULL, user_id INT NOT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_3F68B664B3EC99FE (achievement_id), INDEX IDX_3F68B664A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_education (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, edu_id INT DEFAULT NULL, rating INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_DBEAD336A76ED395 (user_id), INDEX IDX_DBEAD33640A8A24B (edu_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE education ADD CONSTRAINT FK_DB0A5ED21F55203D FOREIGN KEY (topic_id) REFERENCES topic (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE education_tasks ADD CONSTRAINT FK_ECD47F3040A8A24B FOREIGN KEY (edu_id) REFERENCES education (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE education_tasks ADD CONSTRAINT FK_ECD47F308DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB251F55203D FOREIGN KEY (topic_id) REFERENCES topic (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB257E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE task_mark ADD CONSTRAINT FK_15C7660AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_achievement ADD CONSTRAINT FK_3F68B664B3EC99FE FOREIGN KEY (achievement_id) REFERENCES achievement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_achievement ADD CONSTRAINT FK_3F68B664A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_education ADD CONSTRAINT FK_DBEAD336A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE user_education ADD CONSTRAINT FK_DBEAD33640A8A24B FOREIGN KEY (edu_id) REFERENCES education (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE education DROP FOREIGN KEY FK_DB0A5ED21F55203D');
        $this->addSql('ALTER TABLE education_tasks DROP FOREIGN KEY FK_ECD47F3040A8A24B');
        $this->addSql('ALTER TABLE education_tasks DROP FOREIGN KEY FK_ECD47F308DB60186');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB251F55203D');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB257E3C61F9');
        $this->addSql('ALTER TABLE task_mark DROP FOREIGN KEY FK_15C7660AA76ED395');
        $this->addSql('ALTER TABLE user_achievement DROP FOREIGN KEY FK_3F68B664B3EC99FE');
        $this->addSql('ALTER TABLE user_achievement DROP FOREIGN KEY FK_3F68B664A76ED395');
        $this->addSql('ALTER TABLE user_education DROP FOREIGN KEY FK_DBEAD336A76ED395');
        $this->addSql('ALTER TABLE user_education DROP FOREIGN KEY FK_DBEAD33640A8A24B');
        $this->addSql('DROP TABLE achievement');
        $this->addSql('DROP TABLE education');
        $this->addSql('DROP TABLE education_tasks');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE task_mark');
        $this->addSql('DROP TABLE term');
        $this->addSql('DROP TABLE topic');
        $this->addSql('DROP TABLE user_achievement');
        $this->addSql('DROP TABLE user_education');
    }
}

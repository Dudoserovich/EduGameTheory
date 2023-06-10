<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230608045355 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE achievement ADD subject_of_interaction VARCHAR(255) DEFAULT \'задание\' NOT NULL, ADD type_of_interaction VARCHAR(255) DEFAULT \'прохождение\' NOT NULL, ADD need_score INT DEFAULT 1 NOT NULL, ADD need_tries INT DEFAULT NULL, ADD rating INT DEFAULT NULL');
        $this->addSql('ALTER TABLE task_mark ADD count_tries INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_achievement ADD total_score INT NOT NULL, ADD achievement_date DATETIME DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE achievement DROP subject_of_interaction, DROP type_of_interaction, DROP need_score, DROP need_tries, DROP rating');
        $this->addSql('ALTER TABLE task_mark DROP count_tries');
        $this->addSql('ALTER TABLE user_achievement DROP total_score, DROP achievement_date, DROP updated_at');
    }
}

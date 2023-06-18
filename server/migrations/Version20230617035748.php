<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230617035748 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE education ADD description LONGTEXT NOT NULL, ADD conclusion LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE education_tasks ADD theory_text LONGTEXT DEFAULT NULL, ADD is_theory TINYINT(1) DEFAULT NULL, DROP text');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE education DROP description, DROP conclusion');
        $this->addSql('ALTER TABLE education_tasks ADD text VARCHAR(255) NOT NULL, DROP theory_text, DROP is_theory');
    }
}

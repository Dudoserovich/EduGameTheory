<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230220061713 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE literature (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, link VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_5D0BA6BB5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE topic_literature (id INT AUTO_INCREMENT NOT NULL, topic_id INT DEFAULT NULL, literature_id INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_985808911F55203D (topic_id), INDEX IDX_98580891C0C5167B (literature_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE topic_literature ADD CONSTRAINT FK_985808911F55203D FOREIGN KEY (topic_id) REFERENCES topic (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE topic_literature ADD CONSTRAINT FK_98580891C0C5167B FOREIGN KEY (literature_id) REFERENCES literature (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE topic_literature DROP FOREIGN KEY FK_985808911F55203D');
        $this->addSql('ALTER TABLE topic_literature DROP FOREIGN KEY FK_98580891C0C5167B');
        $this->addSql('DROP TABLE literature');
        $this->addSql('DROP TABLE topic_literature');
    }
}

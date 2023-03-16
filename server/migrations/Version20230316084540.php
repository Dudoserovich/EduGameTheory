<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230316084540 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE achievement ADD image_name VARCHAR(255) DEFAULT NULL, ADD image_size INT DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE literature DROP FOREIGN KEY FK_5D0BA6BB1F55203D');
        $this->addSql('DROP INDEX IDX_5D0BA6BB1F55203D ON literature');
        $this->addSql('ALTER TABLE literature DROP topic_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE achievement DROP image_name, DROP image_size, DROP updated_at');
        $this->addSql('ALTER TABLE literature ADD topic_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE literature ADD CONSTRAINT FK_5D0BA6BB1F55203D FOREIGN KEY (topic_id) REFERENCES topic (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_5D0BA6BB1F55203D ON literature (topic_id)');
    }
}

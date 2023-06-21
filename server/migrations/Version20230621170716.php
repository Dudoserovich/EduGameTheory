<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230621170716 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE task ADD name_first_player VARCHAR(255) DEFAULT NULL, ADD name_second_player VARCHAR(255) DEFAULT NULL, ADD name_first_strategies JSON DEFAULT NULL, ADD name_second_strategies JSON DEFAULT NULL, CHANGE matrix matrix JSON NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE avatar avatar VARCHAR(255) DEFAULT \'serious_cat.jpg\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE task DROP name_first_player, DROP name_second_player, DROP name_first_strategies, DROP name_second_strategies, CHANGE matrix matrix JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE avatar avatar VARCHAR(255) DEFAULT \'serious_cat.png\' NOT NULL');
    }
}

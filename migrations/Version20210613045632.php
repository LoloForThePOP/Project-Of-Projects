<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210613045632 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE place (id INT AUTO_INCREMENT NOT NULL, presentation_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, type VARCHAR(40) NOT NULL, latitude VARCHAR(255) NOT NULL, longitude VARCHAR(255) NOT NULL, country VARCHAR(255) DEFAULT NULL, administrative_area_level1 VARCHAR(255) DEFAULT NULL, administrative_area_level2 VARCHAR(255) DEFAULT NULL, locality VARCHAR(255) DEFAULT NULL, sublocality_level1 VARCHAR(255) DEFAULT NULL, postal_code VARCHAR(255) DEFAULT NULL, position SMALLINT DEFAULT NULL, INDEX IDX_741D53CDAB627E8B (presentation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE place ADD CONSTRAINT FK_741D53CDAB627E8B FOREIGN KEY (presentation_id) REFERENCES ppbase (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE place');
    }
}

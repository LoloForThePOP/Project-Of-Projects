<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210607154806 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ppbase (id INT AUTO_INCREMENT NOT NULL, creator_id INT DEFAULT NULL, goal VARCHAR(400) NOT NULL, slug VARCHAR(500) NOT NULL, title VARCHAR(255) DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, keywords VARCHAR(255) DEFAULT NULL, text_description LONGTEXT DEFAULT NULL, status VARCHAR(255) DEFAULT NULL, is_admin_validated TINYINT(1) NOT NULL, overall_quality_assessment SMALLINT DEFAULT NULL, is_published TINYINT(1) NOT NULL, is_deleted TINYINT(1) DEFAULT NULL, created_at DATETIME NOT NULL, views_count INT DEFAULT NULL, parameters JSON DEFAULT NULL, INDEX IDX_A2C26DD061220EA6 (creator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ppbase ADD CONSTRAINT FK_A2C26DD061220EA6 FOREIGN KEY (creator_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE ppbase');
    }
}

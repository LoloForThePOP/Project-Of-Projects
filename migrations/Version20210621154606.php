<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210621154606 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE external_contributors_structure (id INT AUTO_INCREMENT NOT NULL, presentation_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, position SMALLINT DEFAULT NULL, rich_text_content LONGTEXT DEFAULT NULL, INDEX IDX_D9204514AB627E8B (presentation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE persorg (id INT AUTO_INCREMENT NOT NULL, external_contributors_structure_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, missions VARCHAR(255) DEFAULT NULL, position SMALLINT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, website1 VARCHAR(255) DEFAULT NULL, website2 VARCHAR(255) DEFAULT NULL, website3 VARCHAR(255) DEFAULT NULL, postal_mail LONGTEXT DEFAULT NULL, tel1 VARCHAR(255) DEFAULT NULL, tel2 VARCHAR(255) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, INDEX IDX_5EF14EF3CB0E55CD (external_contributors_structure_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE external_contributors_structure ADD CONSTRAINT FK_D9204514AB627E8B FOREIGN KEY (presentation_id) REFERENCES ppbase (id)');
        $this->addSql('ALTER TABLE persorg ADD CONSTRAINT FK_5EF14EF3CB0E55CD FOREIGN KEY (external_contributors_structure_id) REFERENCES external_contributors_structure (id)');
        $this->addSql('ALTER TABLE user ADD persorg_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6497583A8E6 FOREIGN KEY (persorg_id) REFERENCES persorg (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6497583A8E6 ON user (persorg_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE persorg DROP FOREIGN KEY FK_5EF14EF3CB0E55CD');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6497583A8E6');
        $this->addSql('DROP TABLE external_contributors_structure');
        $this->addSql('DROP TABLE persorg');
        $this->addSql('DROP INDEX UNIQ_8D93D6497583A8E6 ON user');
        $this->addSql('ALTER TABLE user DROP persorg_id');
    }
}

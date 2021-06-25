<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210625063456 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE persorg DROP FOREIGN KEY FK_5EF14EF3CB0E55CD');
        $this->addSql('CREATE TABLE contributor_structure (id INT AUTO_INCREMENT NOT NULL, presentation_id INT DEFAULT NULL, type VARCHAR(50) NOT NULL, title VARCHAR(255) DEFAULT NULL, position SMALLINT DEFAULT NULL, rich_text_content LONGTEXT DEFAULT NULL, INDEX IDX_A08D64F6AB627E8B (presentation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE contributor_structure ADD CONSTRAINT FK_A08D64F6AB627E8B FOREIGN KEY (presentation_id) REFERENCES ppbase (id)');
        $this->addSql('DROP TABLE external_contributors_structure');
        $this->addSql('DROP INDEX IDX_5EF14EF3CB0E55CD ON persorg');
        $this->addSql('ALTER TABLE persorg CHANGE external_contributors_structure_id contributor_structure_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE persorg ADD CONSTRAINT FK_5EF14EF345909BCC FOREIGN KEY (contributor_structure_id) REFERENCES contributor_structure (id)');
        $this->addSql('CREATE INDEX IDX_5EF14EF345909BCC ON persorg (contributor_structure_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE persorg DROP FOREIGN KEY FK_5EF14EF345909BCC');
        $this->addSql('CREATE TABLE external_contributors_structure (id INT AUTO_INCREMENT NOT NULL, presentation_id INT DEFAULT NULL, title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, position SMALLINT DEFAULT NULL, rich_text_content LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_D9204514AB627E8B (presentation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE external_contributors_structure ADD CONSTRAINT FK_D9204514AB627E8B FOREIGN KEY (presentation_id) REFERENCES ppbase (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('DROP TABLE contributor_structure');
        $this->addSql('DROP INDEX IDX_5EF14EF345909BCC ON persorg');
        $this->addSql('ALTER TABLE persorg CHANGE contributor_structure_id external_contributors_structure_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE persorg ADD CONSTRAINT FK_5EF14EF3CB0E55CD FOREIGN KEY (external_contributors_structure_id) REFERENCES external_contributors_structure (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_5EF14EF3CB0E55CD ON persorg (external_contributors_structure_id)');
    }
}

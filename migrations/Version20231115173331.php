<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231115173331 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE follow (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, projects_id INT DEFAULT NULL, INDEX IDX_68344470A76ED395 (user_id), INDEX IDX_683444701EDE0F55 (projects_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE follow ADD CONSTRAINT FK_68344470A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE follow ADD CONSTRAINT FK_683444701EDE0F55 FOREIGN KEY (projects_id) REFERENCES ppbase (id)');
        $this->addSql('ALTER TABLE follows DROP FOREIGN KEY FK_4B638A73A76ED395');
        $this->addSql('ALTER TABLE follows_ppbase DROP FOREIGN KEY FK_ABDDA5FA25215351');
        $this->addSql('ALTER TABLE follows_ppbase DROP FOREIGN KEY FK_ABDDA5FA4EF146D4');
        $this->addSql('DROP TABLE follows');
        $this->addSql('DROP TABLE follows_ppbase');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE follows (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_4B638A73A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE follows_ppbase (follows_id INT NOT NULL, ppbase_id INT NOT NULL, INDEX IDX_ABDDA5FA25215351 (follows_id), INDEX IDX_ABDDA5FA4EF146D4 (ppbase_id), PRIMARY KEY(follows_id, ppbase_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE follows ADD CONSTRAINT FK_4B638A73A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE follows_ppbase ADD CONSTRAINT FK_ABDDA5FA25215351 FOREIGN KEY (follows_id) REFERENCES follows (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE follows_ppbase ADD CONSTRAINT FK_ABDDA5FA4EF146D4 FOREIGN KEY (ppbase_id) REFERENCES ppbase (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE follow DROP FOREIGN KEY FK_68344470A76ED395');
        $this->addSql('ALTER TABLE follow DROP FOREIGN KEY FK_683444701EDE0F55');
        $this->addSql('DROP TABLE follow');
    }
}

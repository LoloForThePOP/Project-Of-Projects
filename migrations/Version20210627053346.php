<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210627053346 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE persorg DROP FOREIGN KEY FK_5EF14EF345909BCC');
        $this->addSql('ALTER TABLE persorg ADD CONSTRAINT FK_5EF14EF345909BCC FOREIGN KEY (contributor_structure_id) REFERENCES contributor_structure (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE persorg DROP FOREIGN KEY FK_5EF14EF345909BCC');
        $this->addSql('ALTER TABLE persorg ADD CONSTRAINT FK_5EF14EF345909BCC FOREIGN KEY (contributor_structure_id) REFERENCES contributor_structure (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210627053249 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contributor_structure DROP FOREIGN KEY FK_A08D64F6AB627E8B');
        $this->addSql('ALTER TABLE contributor_structure ADD CONSTRAINT FK_A08D64F6AB627E8B FOREIGN KEY (presentation_id) REFERENCES ppbase (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE need DROP FOREIGN KEY FK_E6F46C44AB627E8B');
        $this->addSql('ALTER TABLE need ADD CONSTRAINT FK_E6F46C44AB627E8B FOREIGN KEY (presentation_id) REFERENCES ppbase (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE place DROP FOREIGN KEY FK_741D53CDAB627E8B');
        $this->addSql('ALTER TABLE place ADD CONSTRAINT FK_741D53CDAB627E8B FOREIGN KEY (presentation_id) REFERENCES ppbase (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contributor_structure DROP FOREIGN KEY FK_A08D64F6AB627E8B');
        $this->addSql('ALTER TABLE contributor_structure ADD CONSTRAINT FK_A08D64F6AB627E8B FOREIGN KEY (presentation_id) REFERENCES ppbase (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE need DROP FOREIGN KEY FK_E6F46C44AB627E8B');
        $this->addSql('ALTER TABLE need ADD CONSTRAINT FK_E6F46C44AB627E8B FOREIGN KEY (presentation_id) REFERENCES ppbase (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE place DROP FOREIGN KEY FK_741D53CDAB627E8B');
        $this->addSql('ALTER TABLE place ADD CONSTRAINT FK_741D53CDAB627E8B FOREIGN KEY (presentation_id) REFERENCES ppbase (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}

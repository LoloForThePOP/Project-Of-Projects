<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210627053032 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE slide DROP FOREIGN KEY FK_72EFEE62AB627E8B');
        $this->addSql('ALTER TABLE slide ADD CONSTRAINT FK_72EFEE62AB627E8B FOREIGN KEY (presentation_id) REFERENCES ppbase (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE slide DROP FOREIGN KEY FK_72EFEE62AB627E8B');
        $this->addSql('ALTER TABLE slide ADD CONSTRAINT FK_72EFEE62AB627E8B FOREIGN KEY (presentation_id) REFERENCES ppbase (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}

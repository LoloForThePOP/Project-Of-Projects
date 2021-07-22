<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210627052833 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ppbase DROP FOREIGN KEY FK_A2C26DD061220EA6');
        $this->addSql('ALTER TABLE ppbase ADD CONSTRAINT FK_A2C26DD061220EA6 FOREIGN KEY (creator_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ppbase DROP FOREIGN KEY FK_A2C26DD061220EA6');
        $this->addSql('ALTER TABLE ppbase ADD CONSTRAINT FK_A2C26DD061220EA6 FOREIGN KEY (creator_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}

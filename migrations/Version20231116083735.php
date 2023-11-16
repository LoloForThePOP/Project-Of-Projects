<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231116083735 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE follow DROP FOREIGN KEY FK_683444701EDE0F55');
        $this->addSql('DROP INDEX IDX_683444701EDE0F55 ON follow');
        $this->addSql('ALTER TABLE follow CHANGE projects_id project_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE follow ADD CONSTRAINT FK_68344470166D1F9C FOREIGN KEY (project_id) REFERENCES ppbase (id)');
        $this->addSql('CREATE INDEX IDX_68344470166D1F9C ON follow (project_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE follow DROP FOREIGN KEY FK_68344470166D1F9C');
        $this->addSql('DROP INDEX IDX_68344470166D1F9C ON follow');
        $this->addSql('ALTER TABLE follow CHANGE project_id projects_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE follow ADD CONSTRAINT FK_683444701EDE0F55 FOREIGN KEY (projects_id) REFERENCES ppbase (id)');
        $this->addSql('CREATE INDEX IDX_683444701EDE0F55 ON follow (projects_id)');
    }
}

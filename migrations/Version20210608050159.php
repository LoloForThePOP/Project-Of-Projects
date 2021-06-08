<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210608050159 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, unique_name VARCHAR(15) NOT NULL, description_en VARCHAR(100) NOT NULL, description_fr VARCHAR(100) DEFAULT NULL, position SMALLINT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category_ppbase (category_id INT NOT NULL, ppbase_id INT NOT NULL, INDEX IDX_AD2AA7AB12469DE2 (category_id), INDEX IDX_AD2AA7AB4EF146D4 (ppbase_id), PRIMARY KEY(category_id, ppbase_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category_ppbase ADD CONSTRAINT FK_AD2AA7AB12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_ppbase ADD CONSTRAINT FK_AD2AA7AB4EF146D4 FOREIGN KEY (ppbase_id) REFERENCES ppbase (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category_ppbase DROP FOREIGN KEY FK_AD2AA7AB12469DE2');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE category_ppbase');
    }
}

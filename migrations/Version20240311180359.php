<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240311180359 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bank_account (id INT AUTO_INCREMENT NOT NULL, surname_name VARCHAR(255) NOT NULL, iban VARCHAR(255) NOT NULL, bic VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ppbase ADD bank_account_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ppbase ADD CONSTRAINT FK_A2C26DD012CB990C FOREIGN KEY (bank_account_id) REFERENCES bank_account (id)');
        $this->addSql('CREATE INDEX IDX_A2C26DD012CB990C ON ppbase (bank_account_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ppbase DROP FOREIGN KEY FK_A2C26DD012CB990C');
        $this->addSql('DROP TABLE bank_account');
        $this->addSql('DROP INDEX IDX_A2C26DD012CB990C ON ppbase');
        $this->addSql('ALTER TABLE ppbase DROP bank_account_id');
    }
}

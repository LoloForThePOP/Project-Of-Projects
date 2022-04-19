<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220418070031 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE purchase (id INT AUTO_INCREMENT NOT NULL, registred_user_id INT DEFAULT NULL, buyer_email VARCHAR(255) NOT NULL, buyer_info JSON DEFAULT NULL, content JSON NOT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_6117D13BC7B276E6 (registred_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13BC7B276E6 FOREIGN KEY (registred_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE category CHANGE unique_name unique_name VARCHAR(15) NOT NULL, CHANGE description_en description_en VARCHAR(100) NOT NULL, CHANGE description_fr description_fr VARCHAR(100) DEFAULT NULL, CHANGE image image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE category_ppbase ADD CONSTRAINT FK_AD2AA7AB12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_ppbase ADD CONSTRAINT FK_AD2AA7AB4EF146D4 FOREIGN KEY (ppbase_id) REFERENCES ppbase (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE contributor_structure CHANGE type type VARCHAR(50) NOT NULL, CHANGE title title VARCHAR(255) DEFAULT NULL, CHANGE rich_text_content rich_text_content LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE contributor_structure ADD CONSTRAINT FK_A08D64F6AB627E8B FOREIGN KEY (presentation_id) REFERENCES ppbase (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE conversation CHANGE context context VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E9AB627E8B FOREIGN KEY (presentation_id) REFERENCES ppbase (id)');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E9E2544CD6 FOREIGN KEY (author_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE conversation_user ADD CONSTRAINT FK_5AECB5559AC0396 FOREIGN KEY (conversation_id) REFERENCES conversation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE conversation_user ADD CONSTRAINT FK_5AECB555A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE document CHANGE title title VARCHAR(255) NOT NULL, CHANGE file_name file_name VARCHAR(255) NOT NULL, CHANGE mime_type mime_type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A76AB627E8B FOREIGN KEY (presentation_id) REFERENCES ppbase (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message CHANGE type type VARCHAR(50) NOT NULL, CHANGE context context VARCHAR(150) DEFAULT NULL, CHANGE content content LONGTEXT NOT NULL, CHANGE author_email author_email VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FE2544CD6 FOREIGN KEY (author_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F9AC0396 FOREIGN KEY (conversation_id) REFERENCES conversation (id)');
        $this->addSql('ALTER TABLE need CHANGE type type VARCHAR(100) DEFAULT NULL, CHANGE title title VARCHAR(255) NOT NULL, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE is_paid is_paid VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE need ADD CONSTRAINT FK_E6F46C44AB627E8B FOREIGN KEY (presentation_id) REFERENCES ppbase (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE persorg CHANGE name name VARCHAR(255) NOT NULL, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE missions missions VARCHAR(255) DEFAULT NULL, CHANGE email email VARCHAR(255) DEFAULT NULL, CHANGE website1 website1 VARCHAR(255) DEFAULT NULL, CHANGE website2 website2 VARCHAR(255) DEFAULT NULL, CHANGE website3 website3 VARCHAR(255) DEFAULT NULL, CHANGE postal_mail postal_mail LONGTEXT DEFAULT NULL, CHANGE tel1 tel1 VARCHAR(255) DEFAULT NULL, CHANGE tel2 tel2 VARCHAR(255) DEFAULT NULL, CHANGE image image VARCHAR(255) DEFAULT NULL, CHANGE website4 website4 VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE persorg ADD CONSTRAINT FK_5EF14EF345909BCC FOREIGN KEY (contributor_structure_id) REFERENCES contributor_structure (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE place CHANGE name name VARCHAR(255) DEFAULT NULL, CHANGE type type VARCHAR(40) NOT NULL, CHANGE latitude latitude VARCHAR(255) NOT NULL, CHANGE longitude longitude VARCHAR(255) NOT NULL, CHANGE country country VARCHAR(255) DEFAULT NULL, CHANGE administrative_area_level1 administrative_area_level1 VARCHAR(255) DEFAULT NULL, CHANGE administrative_area_level2 administrative_area_level2 VARCHAR(255) DEFAULT NULL, CHANGE locality locality VARCHAR(255) DEFAULT NULL, CHANGE sublocality_level1 sublocality_level1 VARCHAR(255) DEFAULT NULL, CHANGE postal_code postal_code VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE place ADD CONSTRAINT FK_741D53CDAB627E8B FOREIGN KEY (presentation_id) REFERENCES ppbase (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ppbase CHANGE goal goal VARCHAR(400) NOT NULL, CHANGE title title VARCHAR(255) DEFAULT NULL, CHANGE logo logo VARCHAR(255) DEFAULT NULL, CHANGE keywords keywords VARCHAR(255) DEFAULT NULL, CHANGE text_description text_description LONGTEXT DEFAULT NULL, CHANGE string_id string_id VARCHAR(191) NOT NULL');
        $this->addSql('ALTER TABLE ppbase ADD CONSTRAINT FK_A2C26DD061220EA6 FOREIGN KEY (creator_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A2C26DD04AC2F1F0 ON ppbase (string_id)');
        $this->addSql('ALTER TABLE slide ADD licence VARCHAR(255) DEFAULT NULL, CHANGE type type VARCHAR(30) NOT NULL, CHANGE caption caption VARCHAR(400) DEFAULT NULL, CHANGE address address VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE slide ADD CONSTRAINT FK_72EFEE62AB627E8B FOREIGN KEY (presentation_id) REFERENCES ppbase (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user CHANGE email email VARCHAR(180) NOT NULL, CHANGE password password VARCHAR(255) NOT NULL, CHANGE email_validation_token email_validation_token VARCHAR(255) DEFAULT NULL, CHANGE reset_password_token reset_password_token VARCHAR(255) DEFAULT NULL, CHANGE user_name user_name VARCHAR(30) NOT NULL, CHANGE user_name_slug user_name_slug VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6497583A8E6 FOREIGN KEY (persorg_id) REFERENCES persorg (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE purchase');
        $this->addSql('ALTER TABLE category CHANGE unique_name unique_name VARCHAR(15) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE description_en description_en VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE description_fr description_fr VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE image image VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE category_ppbase DROP FOREIGN KEY FK_AD2AA7AB12469DE2');
        $this->addSql('ALTER TABLE category_ppbase DROP FOREIGN KEY FK_AD2AA7AB4EF146D4');
        $this->addSql('ALTER TABLE contributor_structure DROP FOREIGN KEY FK_A08D64F6AB627E8B');
        $this->addSql('ALTER TABLE contributor_structure CHANGE type type VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE title title VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE rich_text_content rich_text_content LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E9AB627E8B');
        $this->addSql('ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E9E2544CD6');
        $this->addSql('ALTER TABLE conversation CHANGE context context VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE conversation_user DROP FOREIGN KEY FK_5AECB5559AC0396');
        $this->addSql('ALTER TABLE conversation_user DROP FOREIGN KEY FK_5AECB555A76ED395');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A76AB627E8B');
        $this->addSql('ALTER TABLE document CHANGE title title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE mime_type mime_type VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE file_name file_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FE2544CD6');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F9AC0396');
        $this->addSql('ALTER TABLE message CHANGE type type VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE context context VARCHAR(150) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE content content LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE author_email author_email VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE need DROP FOREIGN KEY FK_E6F46C44AB627E8B');
        $this->addSql('ALTER TABLE need CHANGE type type VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE is_paid is_paid VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE title title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE description description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE persorg DROP FOREIGN KEY FK_5EF14EF345909BCC');
        $this->addSql('ALTER TABLE persorg CHANGE description description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE missions missions VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE email email VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE website1 website1 VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE website2 website2 VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE website3 website3 VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE website4 website4 VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE postal_mail postal_mail LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE tel1 tel1 VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE tel2 tel2 VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE image image VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE name name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE place DROP FOREIGN KEY FK_741D53CDAB627E8B');
        $this->addSql('ALTER TABLE place CHANGE name name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE type type VARCHAR(40) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE latitude latitude VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE longitude longitude VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE country country VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE administrative_area_level1 administrative_area_level1 VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE administrative_area_level2 administrative_area_level2 VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE locality locality VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE sublocality_level1 sublocality_level1 VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE postal_code postal_code VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE ppbase DROP FOREIGN KEY FK_A2C26DD061220EA6');
        $this->addSql('DROP INDEX UNIQ_A2C26DD04AC2F1F0 ON ppbase');
        $this->addSql('ALTER TABLE ppbase CHANGE goal goal VARCHAR(400) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE title title VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE logo logo VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE keywords keywords VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE text_description text_description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE string_id string_id VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE slide DROP FOREIGN KEY FK_72EFEE62AB627E8B');
        $this->addSql('ALTER TABLE slide DROP licence, CHANGE type type VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE address address VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE caption caption VARCHAR(400) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6497583A8E6');
        $this->addSql('ALTER TABLE user CHANGE user_name user_name VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE email email VARCHAR(180) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE password password VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE email_validation_token email_validation_token VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE reset_password_token reset_password_token VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE user_name_slug user_name_slug VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}

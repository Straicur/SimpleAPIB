<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220120102903 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE AdminToken (token_admin_id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, admin_id INTEGER DEFAULT NULL, token VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, active_to DATETIME NOT NULL, active BOOLEAN NOT NULL)');
        $this->addSql('CREATE INDEX IDX_AC977F14642B8210 ON AdminToken (admin_id)');
        $this->addSql('CREATE TABLE AdminUser (admin_id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:array)
        , password VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_943CB15BE7927C74 ON AdminUser (email)');
        $this->addSql('CREATE TABLE Comment (comment_id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, post_id INTEGER DEFAULT NULL, user_id INTEGER DEFAULT NULL, text VARCHAR(180) NOT NULL)');
        $this->addSql('CREATE INDEX IDX_5BC96BF04B89032C ON Comment (post_id)');
        $this->addSql('CREATE INDEX IDX_5BC96BF0A76ED395 ON Comment (user_id)');
        $this->addSql('CREATE TABLE Post (post_id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, admin_id INTEGER DEFAULT NULL, title VARCHAR(180) NOT NULL, text VARCHAR(180) NOT NULL, post_date DATETIME NOT NULL)');
        $this->addSql('CREATE INDEX IDX_FAB8C3B3642B8210 ON Post (admin_id)');
        $this->addSql('CREATE TABLE Token (token_id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, token VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, active_to DATETIME NOT NULL, active BOOLEAN NOT NULL)');
        $this->addSql('CREATE INDEX IDX_9EF68E3FA76ED395 ON Token (user_id)');
        $this->addSql('CREATE TABLE User (user_id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:array)
        , password VARCHAR(255) NOT NULL, isVerified BOOLEAN NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2DA17977E7927C74 ON User (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE AdminToken');
        $this->addSql('DROP TABLE AdminUser');
        $this->addSql('DROP TABLE Comment');
        $this->addSql('DROP TABLE Post');
        $this->addSql('DROP TABLE Token');
        $this->addSql('DROP TABLE User');
    }
}

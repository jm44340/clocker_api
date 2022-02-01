<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220201154649 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_2DF8B3C5A76ED395');
        $this->addSql('DROP INDEX IDX_2DF8B3C5166D1F9C');
        $this->addSql('CREATE TEMPORARY TABLE __temp__entries AS SELECT id, project_id, user_id, datetime FROM entries');
        $this->addSql('DROP TABLE entries');
        $this->addSql('CREATE TABLE entries (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, project_id INTEGER DEFAULT NULL, user_id INTEGER DEFAULT NULL, datetime DATETIME NOT NULL, CONSTRAINT FK_2DF8B3C5166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2DF8B3C5A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO entries (id, project_id, user_id, datetime) SELECT id, project_id, user_id, datetime FROM __temp__entries');
        $this->addSql('DROP TABLE __temp__entries');
        $this->addSql('CREATE INDEX IDX_2DF8B3C5A76ED395 ON entries (user_id)');
        $this->addSql('CREATE INDEX IDX_2DF8B3C5166D1F9C ON entries (project_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_2DF8B3C5166D1F9C');
        $this->addSql('DROP INDEX IDX_2DF8B3C5A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__entries AS SELECT id, project_id, user_id, datetime FROM entries');
        $this->addSql('DROP TABLE entries');
        $this->addSql('CREATE TABLE entries (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, project_id INTEGER DEFAULT NULL, user_id INTEGER DEFAULT NULL, datetime DATETIME NOT NULL)');
        $this->addSql('INSERT INTO entries (id, project_id, user_id, datetime) SELECT id, project_id, user_id, datetime FROM __temp__entries');
        $this->addSql('DROP TABLE __temp__entries');
        $this->addSql('CREATE INDEX IDX_2DF8B3C5166D1F9C ON entries (project_id)');
        $this->addSql('CREATE INDEX IDX_2DF8B3C5A76ED395 ON entries (user_id)');
    }
}

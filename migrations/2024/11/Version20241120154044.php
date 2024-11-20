<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241120154044 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE topics (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(180) NOT NULL, description LONGTEXT NOT NULL, close_time DATETIME NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, uuid VARCHAR(255) NOT NULL, cooperative_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_91F64639D17F50A6 (uuid), INDEX IDX_91F646398D0C5D40 (cooperative_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE votes (id INT AUTO_INCREMENT NOT NULL, choice VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, uuid VARCHAR(255) NOT NULL, topic_id INT DEFAULT NULL, user_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_518B7ACFD17F50A6 (uuid), INDEX IDX_518B7ACF1F55203D (topic_id), INDEX IDX_518B7ACFA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE topics ADD CONSTRAINT FK_91F646398D0C5D40 FOREIGN KEY (cooperative_id) REFERENCES cooperatives (id)');
        $this->addSql('ALTER TABLE votes ADD CONSTRAINT FK_518B7ACF1F55203D FOREIGN KEY (topic_id) REFERENCES topics (id)');
        $this->addSql('ALTER TABLE votes ADD CONSTRAINT FK_518B7ACFA76ED395 FOREIGN KEY (user_id) REFERENCES `users` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE topics DROP FOREIGN KEY FK_91F646398D0C5D40');
        $this->addSql('ALTER TABLE votes DROP FOREIGN KEY FK_518B7ACF1F55203D');
        $this->addSql('ALTER TABLE votes DROP FOREIGN KEY FK_518B7ACFA76ED395');
        $this->addSql('DROP TABLE topics');
        $this->addSql('DROP TABLE votes');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241120145722 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cooperatives (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(180) NOT NULL, fantasy_name VARCHAR(180) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, uuid VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_DCA86063D17F50A6 (uuid), UNIQUE INDEX UNIQ_IDENTIFIER_NAME (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE users ADD cooperative_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E98D0C5D40 FOREIGN KEY (cooperative_id) REFERENCES cooperatives (id)');
        $this->addSql('CREATE INDEX IDX_1483A5E98D0C5D40 ON users (cooperative_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE cooperatives');
        $this->addSql('ALTER TABLE `users` DROP FOREIGN KEY FK_1483A5E98D0C5D40');
        $this->addSql('DROP INDEX IDX_1483A5E98D0C5D40 ON `users`');
        $this->addSql('ALTER TABLE `users` DROP cooperative_id');
    }
}

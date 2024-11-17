<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241117150744 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('
            CREATE TABLE `user` (
                id INT AUTO_INCREMENT NOT NULL, 
                uuid VARCHAR(255) NOT NULL, 
                name VARCHAR(180) NOT NULL, 
                username VARCHAR(180) NOT NULL, 
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, 
                updated_at DATETIME DEFAULT NULL, 
                deleted_at DATETIME DEFAULT NULL, 
                UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username), 
                PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE `user`');
    }
}

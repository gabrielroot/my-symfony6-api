<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241124175814 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE addresses (id INT AUTO_INCREMENT NOT NULL, street VARCHAR(255) NOT NULL, uf VARCHAR(255) NOT NULL, state VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, number VARCHAR(255) NOT NULL, complement VARCHAR(255) NOT NULL, zip_code VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, uuid VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_6FCA7516D17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE cooperatives ADD address_id INT NOT NULL');
        $this->addSql('ALTER TABLE cooperatives ADD CONSTRAINT FK_DCA86063F5B7AF75 FOREIGN KEY (address_id) REFERENCES addresses (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DCA86063F5B7AF75 ON cooperatives (address_id)');
        $this->addSql('ALTER TABLE users ADD address_id INT NOT NULL');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9F5B7AF75 FOREIGN KEY (address_id) REFERENCES addresses (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9F5B7AF75 ON users (address_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE addresses');
        $this->addSql('ALTER TABLE `users` DROP FOREIGN KEY FK_1483A5E9F5B7AF75');
        $this->addSql('DROP INDEX UNIQ_1483A5E9F5B7AF75 ON `users`');
        $this->addSql('ALTER TABLE `users` DROP address_id');
        $this->addSql('ALTER TABLE cooperatives DROP FOREIGN KEY FK_DCA86063F5B7AF75');
        $this->addSql('DROP INDEX UNIQ_DCA86063F5B7AF75 ON cooperatives');
        $this->addSql('ALTER TABLE cooperatives DROP address_id');
    }
}

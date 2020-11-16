<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201116113716 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comments (id INT AUTO_INCREMENT NOT NULL, ticket_id_id INT NOT NULL, user_id_id INT DEFAULT NULL, comment VARCHAR(255) NOT NULL, is_private TINYINT(1) NOT NULL, INDEX IDX_5F9E962A5774FDDC (ticket_id_id), INDEX IDX_5F9E962A9D86650F (user_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tickets (id INT AUTO_INCREMENT NOT NULL, assigned_to_id INT NOT NULL, title VARCHAR(255) NOT NULL, message VARCHAR(255) NOT NULL, date_time DATETIME NOT NULL, status VARCHAR(255) NOT NULL, priority TINYINT(1) NOT NULL, INDEX IDX_54469DF4F4BD7827 (assigned_to_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tickets_users (tickets_id INT NOT NULL, users_id INT NOT NULL, INDEX IDX_ECE470568FDC0E9A (tickets_id), INDEX IDX_ECE4705667B3B43D (users_id), PRIMARY KEY(tickets_id, users_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A5774FDDC FOREIGN KEY (ticket_id_id) REFERENCES tickets (id)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A9D86650F FOREIGN KEY (user_id_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE tickets ADD CONSTRAINT FK_54469DF4F4BD7827 FOREIGN KEY (assigned_to_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE tickets_users ADD CONSTRAINT FK_ECE470568FDC0E9A FOREIGN KEY (tickets_id) REFERENCES tickets (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tickets_users ADD CONSTRAINT FK_ECE4705667B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A5774FDDC');
        $this->addSql('ALTER TABLE tickets_users DROP FOREIGN KEY FK_ECE470568FDC0E9A');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A9D86650F');
        $this->addSql('ALTER TABLE tickets DROP FOREIGN KEY FK_54469DF4F4BD7827');
        $this->addSql('ALTER TABLE tickets_users DROP FOREIGN KEY FK_ECE4705667B3B43D');
        $this->addSql('DROP TABLE comments');
        $this->addSql('DROP TABLE tickets');
        $this->addSql('DROP TABLE tickets_users');
        $this->addSql('DROP TABLE users');
    }
}

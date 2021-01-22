<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201224105427 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ban (id INT AUTO_INCREMENT NOT NULL, id_player_id INT NOT NULL, id_server_id INT NOT NULL, id_operator_id INT NOT NULL, start_date DATETIME NOT NULL, is_perma TINYINT(1) DEFAULT NULL, reason VARCHAR(500) NOT NULL, description VARCHAR(1000) DEFAULT NULL, image_password VARCHAR(255) DEFAULT NULL, created DATETIME NOT NULL, updated DATETIME DEFAULT NULL, INDEX IDX_62FED0E519D349F8 (id_player_id), INDEX IDX_62FED0E598715A90 (id_server_id), INDEX IDX_62FED0E540575554 (id_operator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE player (id INT AUTO_INCREMENT NOT NULL, uuid VARCHAR(36) NOT NULL, name VARCHAR(255) NOT NULL, reputation INT DEFAULT NULL, created DATETIME NOT NULL, updated DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE server (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, created DATETIME NOT NULL, updated DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ban ADD CONSTRAINT FK_62FED0E519D349F8 FOREIGN KEY (id_player_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE ban ADD CONSTRAINT FK_62FED0E598715A90 FOREIGN KEY (id_server_id) REFERENCES server (id)');
        $this->addSql('ALTER TABLE ban ADD CONSTRAINT FK_62FED0E540575554 FOREIGN KEY (id_operator_id) REFERENCES player (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ban DROP FOREIGN KEY FK_62FED0E519D349F8');
        $this->addSql('ALTER TABLE ban DROP FOREIGN KEY FK_62FED0E540575554');
        $this->addSql('ALTER TABLE ban DROP FOREIGN KEY FK_62FED0E598715A90');
        $this->addSql('DROP TABLE ban');
        $this->addSql('DROP TABLE player');
        $this->addSql('DROP TABLE server');
    }
}

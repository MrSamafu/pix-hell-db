<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251125000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add new fields to User (firstName, lastName, bio, birthDate, associationJoinDate) and create Badge entity with ManyToMany relation';
    }

    public function up(Schema $schema): void
    {
        // Create badge table
        $this->addSql('CREATE TABLE badge (
            id INT AUTO_INCREMENT NOT NULL, 
            name VARCHAR(255) NOT NULL, 
            image_url VARCHAR(255) NOT NULL, 
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Create user_badge join table
        $this->addSql('CREATE TABLE user_badge (
            user_id INT NOT NULL, 
            badge_id INT NOT NULL, 
            INDEX IDX_1C32B345A76ED395 (user_id), 
            INDEX IDX_1C32B345F7A2C2FC (badge_id), 
            PRIMARY KEY(user_id, badge_id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Add new fields to user table
        $this->addSql('ALTER TABLE user 
            ADD first_name VARCHAR(255) DEFAULT NULL, 
            ADD last_name VARCHAR(255) DEFAULT NULL, 
            ADD bio LONGTEXT DEFAULT NULL, 
            ADD birth_date DATE DEFAULT NULL, 
            ADD association_join_date DATE DEFAULT NULL');

        // Add foreign keys for user_badge
        $this->addSql('ALTER TABLE user_badge 
            ADD CONSTRAINT FK_1C32B345A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_badge 
            ADD CONSTRAINT FK_1C32B345F7A2C2FC FOREIGN KEY (badge_id) REFERENCES badge (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // Drop foreign keys
        $this->addSql('ALTER TABLE user_badge DROP FOREIGN KEY FK_1C32B345A76ED395');
        $this->addSql('ALTER TABLE user_badge DROP FOREIGN KEY FK_1C32B345F7A2C2FC');

        // Drop tables
        $this->addSql('DROP TABLE badge');
        $this->addSql('DROP TABLE user_badge');

        // Remove fields from user table
        $this->addSql('ALTER TABLE user 
            DROP first_name, 
            DROP last_name, 
            DROP bio, 
            DROP birth_date, 
            DROP association_join_date');
    }
}


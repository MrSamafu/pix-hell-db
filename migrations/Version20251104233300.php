<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251104233300 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE accessory (id INT AUTO_INCREMENT NOT NULL, creator_id INT NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(100) NOT NULL, compatibility VARCHAR(255) NOT NULL, INDEX IDX_A1B1251C61220EA6 (creator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE accessory_collection (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, accessory_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_AB66381EA76ED395 (user_id), INDEX IDX_AB66381E27E8CC78 (accessory_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE console (id INT AUTO_INCREMENT NOT NULL, creator_id INT NOT NULL, name VARCHAR(255) NOT NULL, manufacturer VARCHAR(255) NOT NULL, release_date DATE NOT NULL, generation INT NOT NULL, INDEX IDX_3603CFB661220EA6 (creator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE console_collection (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, console_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_DBA31299A76ED395 (user_id), INDEX IDX_DBA3129972F9DD9F (console_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, creator_id INT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, platform VARCHAR(255) NOT NULL, release_date DATE NOT NULL, genre VARCHAR(100) NOT NULL, INDEX IDX_232B318C61220EA6 (creator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_collection (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, game_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_1291A2DFA76ED395 (user_id), INDEX IDX_1291A2DFE48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE accessory ADD CONSTRAINT FK_A1B1251C61220EA6 FOREIGN KEY (creator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE accessory_collection ADD CONSTRAINT FK_AB66381EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE accessory_collection ADD CONSTRAINT FK_AB66381E27E8CC78 FOREIGN KEY (accessory_id) REFERENCES accessory (id)');
        $this->addSql('ALTER TABLE console ADD CONSTRAINT FK_3603CFB661220EA6 FOREIGN KEY (creator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE console_collection ADD CONSTRAINT FK_DBA31299A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE console_collection ADD CONSTRAINT FK_DBA3129972F9DD9F FOREIGN KEY (console_id) REFERENCES console (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C61220EA6 FOREIGN KEY (creator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE game_collection ADD CONSTRAINT FK_1291A2DFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE game_collection ADD CONSTRAINT FK_1291A2DFE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE accessory DROP FOREIGN KEY FK_A1B1251C61220EA6');
        $this->addSql('ALTER TABLE accessory_collection DROP FOREIGN KEY FK_AB66381EA76ED395');
        $this->addSql('ALTER TABLE accessory_collection DROP FOREIGN KEY FK_AB66381E27E8CC78');
        $this->addSql('ALTER TABLE console DROP FOREIGN KEY FK_3603CFB661220EA6');
        $this->addSql('ALTER TABLE console_collection DROP FOREIGN KEY FK_DBA31299A76ED395');
        $this->addSql('ALTER TABLE console_collection DROP FOREIGN KEY FK_DBA3129972F9DD9F');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C61220EA6');
        $this->addSql('ALTER TABLE game_collection DROP FOREIGN KEY FK_1291A2DFA76ED395');
        $this->addSql('ALTER TABLE game_collection DROP FOREIGN KEY FK_1291A2DFE48FD905');
        $this->addSql('DROP TABLE accessory');
        $this->addSql('DROP TABLE accessory_collection');
        $this->addSql('DROP TABLE console');
        $this->addSql('DROP TABLE console_collection');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE game_collection');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}

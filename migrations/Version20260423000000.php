<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260423000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create kit tables (game_kit, console_kit, accessory_kit)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE game_kit (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, game_id INT NOT NULL, quantity INT NOT NULL, note LONGTEXT DEFAULT NULL, INDEX IDX_game_kit_user (user_id), INDEX IDX_game_kit_game (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE console_kit (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, console_id INT NOT NULL, quantity INT NOT NULL, note LONGTEXT DEFAULT NULL, INDEX IDX_console_kit_user (user_id), INDEX IDX_console_kit_console (console_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE accessory_kit (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, accessory_id INT NOT NULL, quantity INT NOT NULL, note LONGTEXT DEFAULT NULL, INDEX IDX_accessory_kit_user (user_id), INDEX IDX_accessory_kit_accessory (accessory_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE game_kit ADD CONSTRAINT FK_game_kit_user FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE game_kit ADD CONSTRAINT FK_game_kit_game FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE console_kit ADD CONSTRAINT FK_console_kit_user FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE console_kit ADD CONSTRAINT FK_console_kit_console FOREIGN KEY (console_id) REFERENCES console (id)');
        $this->addSql('ALTER TABLE accessory_kit ADD CONSTRAINT FK_accessory_kit_user FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE accessory_kit ADD CONSTRAINT FK_accessory_kit_accessory FOREIGN KEY (accessory_id) REFERENCES accessory (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE game_kit');
        $this->addSql('DROP TABLE console_kit');
        $this->addSql('DROP TABLE accessory_kit');
    }
}


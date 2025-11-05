<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251105120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Amélioration de la table game : developer, publisher, series, created_at, image ; création des tables genre et mode avec relations ManyToMany';
    }

    public function up(Schema $schema): void
    {
        // Create genre table
        $this->addSql('CREATE TABLE genre (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Create mode table
        $this->addSql('CREATE TABLE mode (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Create pivot table game_genre
        $this->addSql('CREATE TABLE game_genre (game_id INT NOT NULL, genre_id INT NOT NULL, INDEX IDX_GAME_GENRE_GAME_ID (game_id), INDEX IDX_GAME_GENRE_GENRE_ID (genre_id), PRIMARY KEY(game_id, genre_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game_genre ADD CONSTRAINT FK_GAME_GENRE_GAME FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_genre ADD CONSTRAINT FK_GAME_GENRE_GENRE FOREIGN KEY (genre_id) REFERENCES genre (id) ON DELETE CASCADE');

        // Create pivot table game_mode
        $this->addSql('CREATE TABLE game_mode (game_id INT NOT NULL, mode_id INT NOT NULL, INDEX IDX_GAME_MODE_GAME_ID (game_id), INDEX IDX_GAME_MODE_MODE_ID (mode_id), PRIMARY KEY(game_id, mode_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game_mode ADD CONSTRAINT FK_GAME_MODE_GAME FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_mode ADD CONSTRAINT FK_GAME_MODE_MODE FOREIGN KEY (mode_id) REFERENCES mode (id) ON DELETE CASCADE');

        // Alter game table: add developer, publisher, series, created_at, image
        $this->addSql('ALTER TABLE game ADD developer VARCHAR(255) DEFAULT NULL, ADD publisher VARCHAR(255) DEFAULT NULL, ADD series VARCHAR(255) DEFAULT NULL, ADD created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT \'(DC2Type:datetime)\', ADD image VARCHAR(255) DEFAULT NULL');

        // Note: DEFAULT CURRENT_TIMESTAMP requires MySQL/MariaDB. If your platform is strict, adjust as needed.
    }

    public function down(Schema $schema): void
    {
        // Remove added columns from game
        $this->addSql('ALTER TABLE game DROP created_at, DROP image, DROP series, DROP publisher, DROP developer');

        // Drop pivot tables and related foreign keys then drop genre/mode tables
        $this->addSql('ALTER TABLE game_genre DROP FOREIGN KEY FK_GAME_GENRE_GAME');
        $this->addSql('ALTER TABLE game_genre DROP FOREIGN KEY FK_GAME_GENRE_GENRE');
        $this->addSql('DROP TABLE game_genre');

        $this->addSql('ALTER TABLE game_mode DROP FOREIGN KEY FK_GAME_MODE_GAME');
        $this->addSql('ALTER TABLE game_mode DROP FOREIGN KEY FK_GAME_MODE_MODE');
        $this->addSql('DROP TABLE game_mode');

        $this->addSql('DROP TABLE genre');
        $this->addSql('DROP TABLE mode');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251105121500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout des champs added_at, image et max_players à la table console';
    }

    public function up(Schema $schema): void
    {
        // Use nullable DATETIME to maximize compatibility across MariaDB/MySQL versions
        $this->addSql('ALTER TABLE `console` ADD added_at DATETIME DEFAULT NULL, ADD image VARCHAR(255) DEFAULT NULL, ADD max_players INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `console` DROP added_at, DROP image, DROP max_players');
    }
}

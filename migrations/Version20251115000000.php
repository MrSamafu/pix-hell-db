<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251115000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add note field to collection tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE game_collection ADD note LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE console_collection ADD note LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE accessory_collection ADD note LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE game_collection DROP note');
        $this->addSql('ALTER TABLE console_collection DROP note');
        $this->addSql('ALTER TABLE accessory_collection DROP note');
    }
}


<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251105123000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout des champs created_at, image (URL) et release_date à la table accessory';
    }

    public function up(Schema $schema): void
    {
        // Use nullable DATETIME for compatibility, add image (varchar) and release_date (date)
        $this->addSql("ALTER TABLE accessory ADD created_at DATETIME DEFAULT NULL, ADD image VARCHAR(255) DEFAULT NULL, ADD release_date DATE DEFAULT NULL");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE accessory DROP created_at, DROP image, DROP release_date');
    }
}

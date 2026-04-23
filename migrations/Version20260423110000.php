<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260423110000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add inversedBy on GameKit/ConsoleKit/AccessoryKit — no schema change needed (mapping only)';
    }

    public function up(Schema $schema): void
    {
        // Aucune modification de schéma : les relations inverses OneToMany
        // sur Game/Console/Accessory ne créent pas de nouvelles colonnes.
        // Cette migration valide uniquement le mapping Doctrine.
    }

    public function down(Schema $schema): void
    {
    }
}


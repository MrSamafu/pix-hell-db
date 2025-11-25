<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251125000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add description column to badge table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE badge ADD description LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE badge DROP description');
    }
}


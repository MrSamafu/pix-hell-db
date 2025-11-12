<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration pour transformer platform de string en relation ManyToOne avec Console
 */
final class Version20251112000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Transform platform from string to ManyToOne relation with Console';
    }

    public function up(Schema $schema): void
    {
        // Supprimer l'ancienne colonne genre (VARCHAR) qui est maintenant gérée par ManyToMany
        $this->addSql('ALTER TABLE game DROP genre');

        // Ajouter la nouvelle colonne platform_id
        $this->addSql('ALTER TABLE game ADD platform_id INT DEFAULT NULL');

        // Créer la contrainte de clé étrangère
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CFFE6496F FOREIGN KEY (platform_id) REFERENCES console (id)');
        $this->addSql('CREATE INDEX IDX_232B318CFFE6496F ON game (platform_id)');

        // Supprimer l'ancienne colonne platform (string)
        $this->addSql('ALTER TABLE game DROP platform');

        // Rendre releaseDate nullable
        $this->addSql('ALTER TABLE game CHANGE release_date release_date DATE DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // Remettre releaseDate non nullable
        $this->addSql('ALTER TABLE game CHANGE release_date release_date DATE NOT NULL');

        // Recréer la colonne platform (string)
        $this->addSql('ALTER TABLE game ADD platform VARCHAR(255) NOT NULL');

        // Supprimer la relation avec Console
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318CFFE6496F');
        $this->addSql('DROP INDEX IDX_232B318CFFE6496F ON game');
        $this->addSql('ALTER TABLE game DROP platform_id');

        // Recréer la colonne genre (VARCHAR)
        $this->addSql('ALTER TABLE game ADD genre VARCHAR(100) NOT NULL');
    }
}


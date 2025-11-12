# Modifications apportées au formulaire de création/modification de jeux

## Résumé des changements

### 1. **Date d'enregistrement automatique**
- ✅ Le champ `createdAt` n'apparaît plus dans le formulaire
- ✅ La date est automatiquement définie dans le constructeur de l'entité Game
- ✅ Le controller ne gère plus manuellement cette date

### 2. **Description ajoutée au formulaire**
- ✅ Le champ `description` (textarea) est maintenant visible dans les formulaires
- ✅ Positionné juste après le champ "Titre"
- ✅ 4 lignes de hauteur avec placeholder

### 3. **Plateforme en liste déroulante**
- ✅ Le champ `platform` est transformé de `string` en relation `ManyToOne` avec l'entité Console
- ✅ Liste déroulante affichant toutes les consoles enregistrées
- ✅ Affiche le nom de la console (Console::$name)

### 4. **Image en URL (pas de fichier)**
- ✅ Le champ `image` est maintenant un champ URL (TextType/UrlType)
- ✅ Plus d'upload de fichier sur le serveur
- ✅ Le controller ne gère plus l'upload de fichier
- ✅ Placeholder avec exemple d'URL

## Fichiers modifiés

### Entité
- **src/Entity/Game.php**
  - `platform` : `string` → `ManyToOne Console`
  - `releaseDate` : rendu nullable

### Formulaire
- **src/Form/GameType.php**
  - Supprimé : `createdAt` (DateTimeType)
  - Modifié : `platform` → EntityType (liste des consoles)
  - Modifié : `image` → UrlType (au lieu de FileType)
  - Ajouté : placeholders et classes CSS

### Controller
- **src/Controller/GameController.php**
  - Supprimé : gestion de l'upload de fichier
  - Supprimé : gestion manuelle de `createdAt`
  - Supprimé : import de SluggerInterface et FileException

### Templates
- **templates/game/new.html.twig**
  - Ajouté : champ description après le titre
  - Corrigé : champ platform devient une liste déroulante
  - Ajouté : aide contextuelle pour la console

- **templates/game/edit.html.twig**
  - Ajouté : champ description après le titre
  - Corrigé : nom du champ `series` (au lieu de `serie`)
  - Corrigé : nom du champ `modes` (au lieu de `gameModes`)
  - Corrigé : champ platform devient une liste déroulante

- **templates/game/index.html.twig**
  - Modifié : affichage de `game.platform.name` au lieu de `game.platform`

### Migration
- **migrations/Version20251112000000.php**
  - Ajoute la colonne `platform_id`
  - Crée la clé étrangère vers `console`
  - Supprime l'ancienne colonne `platform` (string)
  - Rend `release_date` nullable

## Instructions pour appliquer les changements

### 1. Exécuter la migration

**⚠️ ATTENTION : Cette migration va supprimer les données de la colonne platform (string)**

Avant d'exécuter la migration, vous devriez :
1. Sauvegarder votre base de données
2. Ou créer un script de migration des données pour associer les jeux existants aux consoles

```bash
php bin/console doctrine:migrations:migrate
```

### 2. Option : Migration des données existantes

Si vous avez déjà des jeux dans la base, vous pouvez modifier la migration pour conserver les données :

```php
public function up(Schema $schema): void
{
    // 1. Ajouter la nouvelle colonne
    $this->addSql('ALTER TABLE game ADD platform_id INT DEFAULT NULL');
    
    // 2. Migrer les données (exemple : si platform = "PlayStation 5")
    $this->addSql('
        UPDATE game g
        JOIN console c ON g.platform = c.name
        SET g.platform_id = c.id
    ');
    
    // 3. Rendre platform_id NOT NULL après migration
    $this->addSql('ALTER TABLE game MODIFY platform_id INT NOT NULL');
    
    // 4. Créer la contrainte
    $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CFFE6496F FOREIGN KEY (platform_id) REFERENCES console (id)');
    $this->addSql('CREATE INDEX IDX_232B318CFFE6496F ON game (platform_id)');
    
    // 5. Supprimer l'ancienne colonne
    $this->addSql('ALTER TABLE game DROP platform');
}
```

### 3. Vider le cache Symfony

```bash
php bin/console cache:clear
```

### 4. Recompiler les assets (si nécessaire)

```bash
npm run build
```

## Résultat attendu

### Formulaire de création/modification
- ✅ Titre en haut
- ✅ Description (textarea) juste après
- ✅ Console (liste déroulante) au lieu d'un champ texte
- ✅ Développeur, Éditeur, Date de sortie, Série
- ✅ Image (URL) avec placeholder
- ✅ Genres et Modes (multi-select)
- ❌ Pas de champ "Date d'enregistrement" (automatique)

### Affichage dans la liste
- Les jeux affichent le nom de leur console associée
- Les cartes de jeux sont visuellement cohérentes

## Tests recommandés

1. ✅ Créer un nouveau jeu → vérifier que createdAt est défini automatiquement
2. ✅ Sélectionner une console dans la liste déroulante
3. ✅ Vérifier que l'image URL est bien enregistrée
4. ✅ Modifier un jeu existant → vérifier que la console est pré-sélectionnée
5. ✅ Afficher la liste des jeux → vérifier que les noms de console s'affichent correctement


# Système de Collection - Guide d'utilisation

## Structure de la collection

Le système de collection est divisé en 3 parties principales :

### 1. Ma Collection (`/collection/my`)
- Affiche votre collection personnelle de jeux, consoles et accessoires
- Permet de modifier la quantité de chaque élément
- Permet d'ajouter des notes personnelles sur chaque élément
- Permet de supprimer des éléments de votre collection

### 2. Collections des utilisateurs (`/collection`)
- Liste tous les utilisateurs de l'application
- Permet de consulter la collection de chaque utilisateur
- Vue en lecture seule des collections des autres utilisateurs

### 3. Recherche (`/collection/search`)
- Recherche d'objets (jeux, consoles, accessoires) dans toutes les collections
- Affiche qui possède chaque objet trouvé
- Filtre par type d'objet (tous, jeux, consoles, accessoires)

## Fonctionnalités

### Gestion de la quantité
Chaque élément de votre collection peut avoir une quantité (minimum 1)
- Utile pour les collectionneurs qui possèdent plusieurs exemplaires
- Mise à jour en temps réel via JavaScript

### Notes personnelles
Ajoutez des notes sur vos objets pour préciser :
- L'état de l'objet (neuf, occasion, etc.)
- La provenance (achat, cadeau, etc.)
- Des informations spécifiques (édition limitée, complète, etc.)

### Statistiques
Affichage du nombre total de :
- Jeux dans la collection
- Consoles dans la collection
- Accessoires dans la collection

## Migration de la base de données

Pour ajouter le champ "note" aux tables de collection :

```bash
php bin/console doctrine:migrations:migrate
```

Cette migration ajoute un champ `note` (TEXT, nullable) aux tables :
- `game_collection`
- `console_collection`
- `accessory_collection`

## Routes disponibles

- `GET /collection` - Page d'accueil des collections (3 parties)
- `GET /collection/my` - Ma collection personnelle
- `GET /collection/user/{id}` - Collection d'un utilisateur
- `GET /collection/search` - Recherche dans les collections
- `POST /collection/game/{id}/update` - Mise à jour d'un jeu dans ma collection
- `POST /collection/game/{id}/delete` - Suppression d'un jeu de ma collection
- `POST /collection/console/{id}/update` - Mise à jour d'une console
- `POST /collection/console/{id}/delete` - Suppression d'une console
- `POST /collection/accessory/{id}/update` - Mise à jour d'un accessoire
- `POST /collection/accessory/{id}/delete` - Suppression d'un accessoire

## APIs utilisées

### Mise à jour d'un élément
```javascript
POST /collection/{type}/{id}/update
Body: quantity=2&note=Ma note personnelle
Response: { success: true, message: "Collection mise à jour avec succès" }
```

### Suppression d'un élément
```javascript
POST /collection/{type}/{id}/delete
Response: { success: true, message: "Élément retiré de la collection" }
```

## Sécurité

- Toutes les routes nécessitent d'être authentifié (ROLE_USER)
- Les utilisateurs ne peuvent modifier/supprimer que leurs propres éléments
- Les collections des autres utilisateurs sont en lecture seule

